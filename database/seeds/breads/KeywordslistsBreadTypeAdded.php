<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class KeywordslistsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'keywordslists')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'keywordslists')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 37,
                'name' => 'keywordslists',
                'slug' => 'keywordslists',
                'display_name_singular' => 'Keywordslist',
                'display_name_plural' => 'Keywordslists',
                'icon' => NULL,
                'model_name' => 'App\\Keywordslist',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\KeywordslistController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2020-11-27 02:26:58',
                'updated_at' => '2020-11-27 02:26:58',
            ));

            
            

            Voyager::model('Permission')->generateFor('keywordslists');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Keywordslists',
                'url' => '',
                'route' => 'voyager.keywordslists.index',
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
