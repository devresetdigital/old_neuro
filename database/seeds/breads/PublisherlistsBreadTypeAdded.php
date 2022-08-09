<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class PublisherlistsBreadTypeAdded extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        try {
            \DB::beginTransaction();

            $dataType = DataType::where('name', 'publisherlists')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'publisherlists')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 47,
                'name' => 'publisherlists',
                'slug' => 'publisherlists',
                'display_name_singular' => 'Publisherlist',
                'display_name_plural' => 'Publisherlists',
                'icon' => 'voyager-list',
                'model_name' => 'App\\Publisherlist',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\PublisherlistController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2021-04-12 11:41:09',
                'updated_at' => '2021-05-13 18:32:46',
            ));

            
            

            Voyager::model('Permission')->generateFor('publisherlists');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Publisherlists',
                'url' => '',
                'route' => 'voyager.publisherlists.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-list',
                    'color' => null,
                    'parent_id' => null,
                    'order' => $order,
                ])->save();
            }
        } catch(Exception $e) {
           throw new Exception('Exception occur ' . $e);

           \DB::rollBack();
        }

        \DB::commit();
    }
}
