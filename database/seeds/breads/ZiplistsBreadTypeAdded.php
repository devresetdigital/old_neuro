<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class ZiplistsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'ziplists')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'ziplists')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 23,
                'name' => 'ziplists',
                'slug' => 'ziplists',
                'display_name_singular' => 'Ziplist',
                'display_name_plural' => 'Ziplists',
                'icon' => NULL,
                'model_name' => 'App\\Ziplist',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\ZiplistsController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2019-01-24 18:08:01',
                'updated_at' => '2020-10-28 11:46:03',
            ));

            
            

            Voyager::model('Permission')->generateFor('ziplists');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Ziplists',
                'url' => '',
                'route' => 'voyager.ziplists.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => '',
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
