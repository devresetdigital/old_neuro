<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class AttributionReportBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'conversion_pixels')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'conversion_pixels')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 28,
                'name' => 'conversion_pixels',
                'slug' => 'attribution-report',
                'display_name_singular' => 'Attibution Report',
                'display_name_plural' => 'Attibution Reports',
                'icon' => NULL,
                'model_name' => 'App\\ConversionPixel',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\ConversionPixelsController',
                'description' => NULL,
                'generate_permissions' => 0,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2019-09-29 19:02:17',
                'updated_at' => '2021-05-31 16:23:07',
            ));

            
            

            

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Attibution Reports',
                'url' => '',
                'route' => 'voyager.attribution-report.index',
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
