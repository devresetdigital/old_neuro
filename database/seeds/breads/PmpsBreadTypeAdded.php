<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class PmpsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'pmps')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'pmps')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 25,
                'name' => 'pmps',
                'slug' => 'pmps',
                'display_name_singular' => 'Pmp',
                'display_name_plural' => 'Pmps',
                'icon' => NULL,
                'model_name' => 'App\\Pmp',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\PmpsController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2019-04-09 15:45:24',
                'updated_at' => '2021-10-01 17:24:51',
            ));

            
            

            Voyager::model('Permission')->generateFor('pmps');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Pmps',
                'url' => '',
                'route' => 'voyager.pmps.index',
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
