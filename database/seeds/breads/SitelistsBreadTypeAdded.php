<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class SitelistsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'sitelists')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'sitelists')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 16,
                'name' => 'sitelists',
                'slug' => 'sitelists',
                'display_name_singular' => 'Sitelist',
                'display_name_plural' => 'Sitelists',
                'icon' => 'voyager-list',
                'model_name' => 'App\\Sitelist',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\SitelistController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-09-07 15:06:59',
                'updated_at' => '2021-10-01 17:22:56',
            ));

            
            

            Voyager::model('Permission')->generateFor('sitelists');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Sitelists',
                'url' => '',
                'route' => 'voyager.sitelists.index',
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
