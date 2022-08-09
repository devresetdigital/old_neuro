<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class PixelsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'pixels')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'pixels')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 21,
                'name' => 'pixels',
                'slug' => 'pixels',
                'display_name_singular' => 'Pixel',
                'display_name_plural' => 'Pixels',
                'icon' => 'voyager-file-code',
                'model_name' => 'App\\Pixel',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\PixelController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-10-01 15:08:38',
                'updated_at' => '2021-10-01 17:25:43',
            ));

            
            

            Voyager::model('Permission')->generateFor('pixels');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Pixels',
                'url' => '',
                'route' => 'voyager.pixels.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-file-code',
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
