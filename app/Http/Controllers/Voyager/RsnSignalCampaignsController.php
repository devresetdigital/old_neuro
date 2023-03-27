<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Database\Schema\Column;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;

use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use App\Http\Helpers\UploaderHelper;
use App\RsnXTwoItems;
use App\RsnSignalCampaign;
use App\RsnSignalSignal;
use App\RsnSignalSoma;
use App\RsnSignalMythic;
use App\RsnSignalPathosEthos;
use App\RsnSignalPersonalState;
use App\RsnXTwoItemsData;
use App\Advertiser;
use Illuminate\Support\Facades\Cache;
use Auth;
use JsonSchema\Uri\Retrievers\FileGetContents;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class RsnSignalCampaignsController extends VoyagerBaseController
{
    use BreadRelationshipParser;
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $relationships = $this->getRelationships($dataType);

            $model = app($dataType->model_name);
            $query = $model::select('*')->with($relationships);

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }
            if(Auth::user()->role_id == 6) {
                $query->where("organization_id","=",Auth::user()->organization_id);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'DESC';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide'
        ));
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relationships = $this->getRelationships($dataType);
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $dataTypeContent = call_user_func([$model->with($relationships), 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relationships = $this->getRelationships($dataType);

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? app($dataType->model_name)->with($relationships)->findOrFail($id)
            : DB::table($dataType->name)->where('id', $id)->first(); // If Model doest exist, get data from table name

        foreach ($dataType->editRows as $key => $row) {
            $details = json_decode($row->details);
            $dataType->editRows[$key]['col_width'] = isset($details->width) ? $details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        
        $is_admin = (Auth::user()->role_id == 1) ? true :false;
        if($is_admin){
            $advertisers = Advertiser::all();
        }else{
            $advertisers = Advertiser::where('organization_id' ,Auth::user()->organization_id )->get();
        }

        return Voyager::view($view, compact('dataType', 'advertisers', 'dataTypeContent', 'isModelTranslatable'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {
        
             // add this "IF" on update functions that have a file uploader
             if(!$request->has('assets') || $request->input('assets')==null){
                $request->merge([
                    'assets' => $data->assets,
                ]);
            }else{
                //format data. replace it into request and move files to the correct folder
                $request->merge([
                    'assets' => UploaderHelper::formatData($request->input('assets'),$slug,$data->assets)
                ]);
            }

            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
            /**
             * import data into tables
             */


            if(!$this->import_data($data->file_path,$data->id,$data->type)){
                $errors=['there was an error trying to import the data, please try again'];
                return redirect()->route('voyager.'.$dataType->slug.'.edit', ['user' => $data->id])
                ->with(compact('errors'));
            }

            try {
                $advertiser = Advertiser::find($data->advertiser_id);
                $media_file = json_decode($data->assets, true);
                $path = urlencode($media_file[0]["download_link"]);

                if ($_ENV['APP_DEBUG']) {
                    $url = $_ENV['NOTIFICATIONS_URL']."/send-neuro-notification?campaign_id={$data->id}&campaign_name={$data->name}&campaign_type={$data->type}&assets={$path}&advertiser_name={$advertiser->name}&test=1";
                } else {
                    $url = $_ENV['NOTIFICATIONS_URL']."/send-neuro-notification?campaign_id={$data->id}&campaign_name={$data->name}&campaign_type={$data->type}&assets={$path}&advertiser_name={$advertiser->name}";
                }
                
                file_get_contents($url);

                if ($request->has('file_path') && $request->file_path) {
                    $username = "";

                    if ($_ENV['APP_DEBUG']) {
                        $url = $_ENV['NOTIFICATIONS_URL']."?campaign_name={$data->name}&username={$username}&test=1";
                    } else {
                        $url = $_ENV['NOTIFICATIONS_URL']."?&campaign_name={$data->name}&username={$username}";
                    }

                    file_get_contents($url);
                }

            } catch (\Throwable $th) {
                
            }

            event(new BreadDataUpdated($dataType, $data));

            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;

        foreach ($dataType->addRows as $key => $row) {
            $details = json_decode($row->details);
            $dataType->addRows[$key]['col_width'] = isset($details->width) ? $details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $is_admin = (Auth::user()->role_id == 1) ? true :false;
        if($is_admin){
            $advertisers = Advertiser::all();
        }else{
            $advertisers = Advertiser::where('organization_id' ,Auth::user()->organization_id )->get();
        }

        return Voyager::view($view, compact('dataType', 'advertisers','dataTypeContent', 'isModelTranslatable'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if(!$request->has('organization_id')){
            $request->merge(['organization_id' => Auth::user()->organization_id]);
        }
        

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->has('_validate')) {

            $request->merge([
                'assets' => UploaderHelper::formatData($request->input('assets'),$slug)
            ]);


            $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

            event(new BreadDataAdded($dataType, $data));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $data]);
            }

            /**
             * import data into tables
             */
            if(!$this->import_data($data->file_path,$data->id, $data->type)){
                return response()->json(['errors' => 'There was an error trying to import the data, please try again']);
            }

            if($data->type == 'all'){
                
                $new = $data->replicate();
                $new->type = 'hao';
                $new->save();

                $data->type='x2';
                $data->save();

            }

            try {
                $advertiser = Advertiser::find($data->advertiser_id);
                $media_file = json_decode($data->assets, true);
                $path = urlencode($media_file[0]["download_link"]);
                
                if ($_ENV['APP_DEBUG']) {
                    $url = $_ENV['NOTIFICATIONS_URL']."/send-neuro-notification?campaign_id={$data->id}&campaign_name={$data->name}&campaign_type={$data->type}&assets={$path}&advertiser_name={$advertiser->name}&test=1";
                } else {
                    $url = $_ENV['NOTIFICATIONS_URL']."/send-neuro-notification?campaign_id={$data->id}&campaign_name={$data->name}&campaign_type={$data->type}&assets={$path}&advertiser_name={$advertiser->name}";
                }

                file_get_contents($url);
            } catch (\Throwable $th) {
                
            }
            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                        'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
                        'alert-type' => 'success',
                    ]);
        }
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
            $this->cleanup($dataType, $data);
        }

        $displayName = count($ids) > 1 ? $dataType->display_name_plural : $dataType->display_name_singular;

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    /**
     * Remove translations, images and files related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $dataType
     * @param \Illuminate\Database\Eloquent\Model $data
     *
     * @return void
     */
    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->where('type', 'image'));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            foreach (json_decode($data->{$row->field}) as $file) {
                $this->deleteFileIfExists($file->download_link);
            }
        }
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows)
    {
        foreach ($rows as $row) {
            if ($data->{$row->field} != config('voyager.user.default_avatar')) {
                $this->deleteFileIfExists($data->{$row->field});
            }

            $options = json_decode($row->details);

            if (isset($options->thumbnails)) {
                foreach ($options->thumbnails as $thumbnail) {
                    $ext = explode('.', $data->{$row->field});
                    $extension = '.'.$ext[count($ext) - 1];

                    $path = str_replace($extension, '', $data->{$row->field});

                    $thumb_name = $thumbnail->name;

                    $this->deleteFileIfExists($path.'-'.$thumb_name.$extension);
                }
            }
        }

        if ($rows->count() > 0) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

    /**
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::bread.ordering_not_set'),
                'alert-type' => 'error',
            ]);
        }

        $model = app($dataType->model_name);
        $results = $model->orderBy($dataType->order_column)->get();

        $display_column = $dataType->order_display_column;

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'results'
        ));
    }

    public function update_order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $column = $dataType->order_column;
        foreach ($order as $key => $item) {
            $i = $model->findOrFail($item->id);
            $i->$column = ($key + 1);
            $i->save();
        }
    }

    private function deleteData($campaign_id){

        $signals = RsnSignalSignal::where('signal_campaign_id',$campaign_id)->get()->toArray();
        RsnSignalSignal::where('signal_campaign_id',$campaign_id)->delete();
        RsnXTwoItems::where('signal_campaign_id',$campaign_id)->delete();
        RsnXTwoItemsData::where('signal_campaign_id',$campaign_id)->delete();

        foreach ($signals as $key => $signal) {
            RsnSignalSoma::where('signal_id', $signal['id'])->delete();
            RsnSignalMythic::where('signal_id', $signal['id'])->delete();
            RsnSignalPathosEthos::where('signal_id', $signal['id'])->delete();
            RsnSignalPersonalState::where('signal_id', $signal['id'])->delete();
        }

    }


    private function import_data($path, $campaign_id, $type){

        if($path == null){
            return true;
        }
       
        switch ($type) {
            case 'hao':
                return $this->hao_import($path,$campaign_id);
                break;
            case 'x2':
                return $this->x2_import($path,$campaign_id);
                break;
            
            default:
                # code...
                break;
        }

       

    }

    private function x2_import($path, $campaign_id){
        
        try {

            DB::beginTransaction();

            $this->deleteData($campaign_id);

            $path = json_decode($path,true);
            $path =  public_path('/storage/'.$path[0]['download_link']);
    
            //-----------------read------------------------
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($path);
            
            
          
            $signals=[];

            $sheetCount = $spreadsheet->getSheetCount();
            for ($i = 0; $i < $sheetCount; $i++) {
                $data = $spreadsheet->getSheet($i)->toArray();

                if ($i == 0 ){
                    //-----------------Signals------------------------
                    foreach ($data as $key => $signal) {
                        if($key>0 && $signal[1] !== null){
                            $new = new RsnXTwoItems();
                            $new->name =  $signal[1];
                            $new->signal_campaign_id = $campaign_id;
                            $new->preview =  $signal[2];
                            $new->save();
                            $signals[$signal[0]] = $new->id;
                        }
                    }

                } else {
                    //-----------------Signals Data------------------------
                    $labels =[];
                    foreach ($data as $key => $signal) {
                        
                        if($key == 0 ){
                            foreach ($signal as $k => $label) {
                                if ($k > 1 && $label!=''){
                                    $labels[$k]=$label;
                                }
                            }
                        }

                        if($key!=0 && $signal[0] != '' && array_key_exists($signal[0],$signals)) {
                            $new = new RsnXTwoItemsData();
                            $new->name =  $signal[1];
                            $new->signal_campaign_id = $campaign_id;
                            $new->item_id = $signals[$signal[0]];
                            $data=[];
                            foreach ($signal as $j => $value) {
                                if ($j > 1 && count($labels) > $j ){
                                    $data[$labels[$j]]=$value;
                                }
                            }
                            $new->data =  json_encode($data);
                            $new->save();
                        }
                    }
                }
                  
            }


        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getTrace());
            DB::rollBack();
            return false;
        }

        Cache::forget('signal_campaigns_'.intval($campaign_id));
        DB::commit();

    
        return true;

    }

    private function hao_import($path, $campaign_id){

        try {
            DB::beginTransaction();

            $this->deleteData($campaign_id);


            $path = json_decode($path,true);
            $path =  public_path('/storage/'.$path[0]['download_link']);
    
            //-----------------read------------------------
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($path);
            
            //-----------------Signals------------------------
            $data = $spreadsheet->getSheetByName('Signals')->toArray();
            $signals=[];
            foreach ($data as $key => $signal) {
                if($key>0 && $signal[0] != '' && $signal[1] != '' && $signal[2] != ''){
                    $new = new RsnSignalSignal();
                    $new->name =  $signal[1];
                    $new->preview_url = $signal[2];
                    $new->suitability_score = floatval(str_replace(',','.',$signal[3]));
                    $new->signal_campaign_id = $campaign_id;
                    $new->save();
                    $signals[$signal[0]] = $new->id;
                }
            }
     
            //-----------------Personal Identifier States------------------------
            $data = $spreadsheet->getSheetByName('Personal Identifier States')->toArray();

            $average_index = $this->array_recursive_search_key_map('Average',$data);
   
            foreach ($data as $key => $state) {
                if($key>0 && $state[0] != '' && $state[1] != '' && isset($signals[$state[0]])){
                    $new = new RsnSignalPersonalState();
                    $new->name =  $state[1];
                    $new->signal_id = $signals[$state[0]];
                    $new->data = json_encode($state);
                    $new->average = $state[$average_index[1]] > 0 ? floatval(str_replace(',','.',$state[$average_index[1]])) : 0 ;
                    $new->save();
                }
            }
            //-----------------SomaSemantic Representations------------------------
            $data = $spreadsheet->getSheetByName('SomaSemantic Representations')->toArray();

            $average_index = $this->array_recursive_search_key_map('Average',$data);
   
            foreach ($data as $key => $soma) {
                if($key>0 && $soma[0] != '' && $soma[1] != '' && isset($signals[$soma[0]])){
                    $new = new RsnSignalSoma();
                    $new->name =  $soma[1];
                    $new->signal_id = $signals[$soma[0]];
                    $new->data = json_encode($soma);
                    $new->average = $soma[$average_index[1]] > 0 ? floatval(str_replace(',','.',$soma[$average_index[1]])) : 0 ;
                    $new->save();
                }
            }

            //-----------------Mythic Narrative------------------------
            $data = $spreadsheet->getSheetByName('Mythic Narrative')->toArray();
            
   
            foreach ($data as $key => $mythic) {
                if($key>0 && $mythic[0] != '' && $mythic[1] != '' && isset($signals[$mythic[0]])){
                    $new = new RsnSignalMythic();
                    $new->name =  $mythic[1];
                    $new->signal_id = $signals[$mythic[0]];
                    $new->score = floatval($mythic[2]);
                    $new->save();
                }
            }
            //-----------------PathosEthos------------------------
            $data = $spreadsheet->getSheetByName('PathosEthos')->toArray();
    
   
            foreach ($data as $key => $pathosEthos) {
                if($key>0 && $pathosEthos[0] != '' && $pathosEthos[1] != '' && isset($signals[$pathosEthos[0]])){
                    $new = new RsnSignalPathosEthos();
                    $new->name =  $pathosEthos[1];
                    $new->signal_id = $signals[$pathosEthos[0]];
                    $new->score = floatval($pathosEthos[2]) ;
                    $new->save();
                }
            }

        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getTrace());
            DB::rollBack();
            return false;
        }

        Cache::forget('signal_campaigns_'.intval($campaign_id));
        DB::commit();
        return true;

    }

    private function array_recursive_search_key_map($needle, $haystack) {
        foreach($haystack as $first_level_key=>$value) {
            if ($needle === $value) {
                return array($first_level_key);
            } elseif (is_array($value)) {
                $callback = $this->array_recursive_search_key_map($needle, $value);
                if ($callback) {
                    return array_merge(array($first_level_key), $callback);
                }
            }
        }
        return false;
    }




}
