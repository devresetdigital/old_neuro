<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class ConceptsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'concepts')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'concepts')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 14,
                'name' => 'concepts',
                'slug' => 'concepts',
                'display_name_singular' => 'Concept',
                'display_name_plural' => 'Concepts',
                'icon' => 'voyager-window-list',
                'model_name' => 'App\\Concept',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\ConceptController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-08-24 04:13:27',
                'updated_at' => '2021-10-01 17:14:36',
            ));

            
            

            Voyager::model('Permission')->generateFor('concepts');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Concepts',
                'url' => '',
                'route' => 'voyager.concepts.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-window-list',
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
