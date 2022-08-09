<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class CreativesBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'creatives')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'creatives')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 20,
                'name' => 'creatives',
                'slug' => 'creatives',
                'display_name_singular' => 'Creative',
                'display_name_plural' => 'Creatives',
                'icon' => 'voyager-images',
                'model_name' => 'App\\Creative',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\CreativeController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-10-01 02:02:58',
                'updated_at' => '2021-09-28 16:08:15',
            ));

            
            

            Voyager::model('Permission')->generateFor('creatives');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Creatives',
                'url' => '',
                'route' => 'voyager.creatives.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-images',
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
