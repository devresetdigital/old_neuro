<?php
namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use DB;
use Log;
use App\Organization;
use App\Advertiser;
use App\LinearData;
use App\Campaign;
use App\Linear;
use Illuminate\Support\Facades\File;

use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;

use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LinearDataController extends VoyagerBaseController
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

        $dates = LinearData::where('linear_id', $id)->get();

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable' , 'dates'));
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

    /**
     * 
     */
    private function getTableData($data, $validation_type ='', $index, $columns, int $excludes=null){
        if ($index == false || $validation_type == '') return [];
        
        $response = [];
        $row = $index[0];
        $col = $index[1];
  

        $brake=false;
       
        while (true) {
            if(count($data) == $row) break;

            switch ($validation_type) {
                case 'string':
                    if($index[0] != $row  && $data[$row][$col]==null) $brake=true;
                    break;
                case 'date':
                    if($index[0] != $row && strpos($data[$row][$col], '/')==false)$brake=true;
                    break;   
                default:
                    # code...
                    break;
            }
            if($brake) break;

            $pre_proccessed = array_slice($data[$row],$col,$columns,false);

            if($excludes!=null){
                unset($pre_proccessed[$excludes]);
                $pre_proccessed=array_values($pre_proccessed);
            }

            $response[]=$pre_proccessed;

            $row++;
        }

        return $response;
    }

    private function exportToCsv($data,$exportPath, $exportName) {

       
        // Open a file in write mode ('w')
        $fp = fopen($exportPath. $exportName, 'w');
        $headers = $data[0];
        unset($data[0]);
        array_walk_recursive($data, array(&$this, 'str_remove_multi'));

        fputcsv($fp, $headers);
        // Loop through file pointer and a line
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $path_to_file = $exportPath. $exportName;
        $file_contents = file_get_contents($path_to_file);
        $file_contents = str_replace('"',"",$file_contents);
        file_put_contents($path_to_file,$file_contents);
    }

    static function str_remove_multi(&$element, $index){
        if (is_a($element, 'DateTime')) {
            $element = $element->format('m/d/y');
        }

        $element = str_replace(",", "", $element);
     
        if($element==''){
            $element=0;
        }
        if(strpos($element, '$')!=false){
            $element = str_replace("$", "", $element);
            $element = str_replace(" ", "", $element);
        }
        if(strpos($element, '%')!=false){
            $element = str_replace("%", "", $element);
            $element = str_replace(" ", "", $element);
            $element = floatval($element) / 100;
        }
        if(strpos($element, ' - ')!=false){
            $element = 0;
        }
        if(strpos($element, 'DIV/0')!=false){
            $element = 0;
        }
        $element = str_replace(" ", "", $element);
    }


    private function exportXlsx(array $params,$model) {

        $tmpPath = public_path('linear/import.xlsx');
        //-----------------read------------------------
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($tmpPath);
        $data = $spreadsheet->getSheetByName($params['sheet'])->toArray();
        //----------------proceso---------------------------
        $index = $this->array_recursive_search_key_map($params['header'],$data);
        $procced_data = $this->getTableData($data,$params['validation_type'],$index,$params['columns'],$params['excludes']);
        //------------------------name----------------------------------
        $date = $model->date;
        $linear_id = $model->linear_id;
        $id = ($_ENV['WL_PREFIX']*1000000) +  $linear_id;
        $date = substr($date, 2,2) . substr($date, 5,2) . substr($date, 8,2);
        $exportPath = public_path('linear/');
        //---------------------exporto---------------
        $exportName =  implode('_',[$id,$date,$params['table_number']]).".csv";

        $this->exportToCsv($procced_data, $exportPath,$exportName  );

        return file_exists($exportPath.$exportName);

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
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

            if($request->file('file') != null) {
                $file = $request->file('file')[0];
                $file->move(public_path('linear'),'import.xlsx');

                //NETWORK
                $params = [
                    'sheet'=>'Pacing Report',
                    'header'=>'Network',
                    'validation_type'=>'string',
                    'columns'=>4,
                    'excludes'=>null,
                    'table_number'=>1
                ];
                $aux = $this->exportXlsx($params,$data);

                //daypart
                $params = [
                    'sheet'=>'Pacing Report',
                    'header'=>'Daypart',
                    'validation_type'=>'string',
                    'columns'=>5,
                    'excludes'=>1,
                    'table_number'=>2
                ];
                $aux = $this->exportXlsx($params,$data);

                //week
                $params = [
                    'sheet'=>'Pacing Report',
                    'header'=>'Week',
                    'validation_type'=>'date',
                    'columns'=>4,
                    'excludes'=>null,
                    'table_number'=>3
                ];
                $aux = $this->exportXlsx($params,$data);

                //DMA Summary
                $params = [
                'sheet'=>'DMA Summary',
                'header'=>'DMA Rank',
                'validation_type'=>'string',
                'columns'=>4,
                'excludes'=>null,
                'table_number'=>4
                ];
                $aux = $this->exportXlsx($params,$data);

                //5.24 Post Logs
                $params = [
                'sheet'=>'5.24 Post Logs',
                'header'=>'Creative Isci',
                'validation_type'=>'string',
                'columns'=>14,
                'excludes'=>null,
                'table_number'=>5
                ];
                $aux = $this->exportXlsx($params,$data);

            }

            event(new BreadDataUpdated($dataType, $data));

            return redirect()
                ->route('voyager.linear.edit', $data->linear_id)
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
        $linear_id = null;

        if(array_key_exists('linear_id', $_GET)){
            $linear_id = $_GET['linear_id'];
        }

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

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable','linear_id'));
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

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->has('_validate')) {
            $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());


            if($request->file('file') != null) {
                $file = $request->file('file')[0];
                $file->move(public_path('linear'),'import.xlsx');

                //NETWORK
                $params = [
                    'sheet'=>'Pacing Report',
                    'header'=>'Network',
                    'validation_type'=>'string',
                    'columns'=>4,
                    'excludes'=>null,
                    'table_number'=>1
                ];
                $aux = $this->exportXlsx($params,$data);

                //daypart
                $params = [
                    'sheet'=>'Pacing Report',
                    'header'=>'Daypart',
                    'validation_type'=>'string',
                    'columns'=>5,
                    'excludes'=>1,
                    'table_number'=>2
                ];
                $aux = $this->exportXlsx($params,$data);

                //week
                $params = [
                    'sheet'=>'Pacing Report',
                    'header'=>'Week',
                    'validation_type'=>'date',
                    'columns'=>4,
                    'excludes'=>null,
                    'table_number'=>3
                ];
                $aux = $this->exportXlsx($params,$data);

                //DMA Summary
                $params = [
                'sheet'=>'DMA Summary',
                'header'=>'DMA Rank',
                'validation_type'=>'string',
                'columns'=>4,
                'excludes'=>null,
                'table_number'=>4
                ];
                $aux = $this->exportXlsx($params,$data);

                //5.24 Post Logs
                $params = [
                'sheet'=>'5.24 Post Logs',
                'header'=>'Creative Isci',
                'validation_type'=>'string',
                'columns'=>14,
                'excludes'=>null,
                'table_number'=>5
                ];
                $aux = $this->exportXlsx($params,$data);

            }



            event(new BreadDataAdded($dataType, $data));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $data]);
            }

            return redirect()
                ->route('voyager.linear.edit', $data->linear_id)
                ->with([
                        'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
                        'alert-type' => 'success',
                    ]);
        }
    }



    public function remove(Request $request, $id){

        $model = LinearData::find($id);
        $date = $model->date;
        $linear_id = $model->linear_id;
        $id = ($_ENV['WL_PREFIX']*1000000) +  $linear_id;
        $date = substr($date, 2,2) . substr($date, 5,2) . substr($date, 8,2);
        $exportPath = public_path('linear/');
        //---------------------exporto---------------
        for ($i=0; $i <= 5; $i++) { 
            $exportName =  implode('_',[$id,$date,$i]).".csv";
            if(file_exists($exportPath.$exportName)) unlink( $exportPath.$exportName);
        }
       
        $model->delete();

        return redirect()
        ->route('voyager.linear.edit',  $linear_id);

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


    /**
     * 
     */
    public  function report(){

        if(!array_key_exists('from', $_GET) || !array_key_exists('until', $_GET)){
            $from=strtotime("-1 week +1 day");
            $until=strtotime('now');
            return redirect('/admin/linear_report?campaign_id='.$_GET['campaign_id']."&from=".date("ymd",$from) ."&until=".date("ymd",$until));
        }

        $id = ($_ENV['WL_PREFIX']*1000000) +  $_GET['campaign_id'];
        $startdate = substr($_GET['from'], 0,2) . substr($_GET['from'], 2,2) . substr($_GET['from'], 4,2);
        $enddate = substr($_GET['until'], 0,2) . substr($_GET['until'], 2,2) . substr($_GET['until'], 4,2);

        $url = "http://134.209.171.185:8000/?campaign=".$id."&data=0&startdate=".$startdate."&enddate=".$enddate;
        $filters = json_decode(file_get_contents($url),true);

        return Voyager::view('voyager::linear.report', compact('filters'));
    }
}
