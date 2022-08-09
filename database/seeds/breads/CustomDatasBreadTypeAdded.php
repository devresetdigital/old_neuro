<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class CustomDatasBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'custom_datas')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'custom_datas')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 29,
                'name' => 'custom_datas',
                'slug' => 'custom-datas',
                'display_name_singular' => 'Custom Data',
                'display_name_plural' => 'Custom Datas',
                'icon' => NULL,
                'model_name' => 'App\\CustomData',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\CustomdatasController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2019-10-11 14:39:55',
                'updated_at' => '2020-10-28 12:09:43',
            ));

            
            

            Voyager::model('Permission')->generateFor('custom_datas');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Custom Datas',
                'url' => '',
                'route' => 'voyager.custom-datas.index',
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
