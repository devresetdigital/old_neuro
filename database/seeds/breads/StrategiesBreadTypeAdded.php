<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class StrategiesBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'strategies')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'strategies')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 22,
                'name' => 'strategies',
                'slug' => 'strategies',
                'display_name_singular' => 'Strategy',
                'display_name_plural' => 'Strategies',
                'icon' => 'voyager-lab',
                'model_name' => 'App\\Strategy',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\StrategyController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-10-02 20:27:36',
                'updated_at' => '2021-09-30 18:59:39',
            ));

            
            

            Voyager::model('Permission')->generateFor('strategies');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Strategies',
                'url' => '',
                'route' => 'voyager.strategies.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-lab',
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
