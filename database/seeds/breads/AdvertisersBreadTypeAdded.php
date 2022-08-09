<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class AdvertisersBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'advertisers')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'advertisers')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 15,
                'name' => 'advertisers',
                'slug' => 'advertisers',
                'display_name_singular' => 'Advertiser',
                'display_name_plural' => 'Advertisers',
                'icon' => 'voyager-group',
                'model_name' => 'App\\Advertiser',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\AdvertiserController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-09-07 14:20:48',
                'updated_at' => '2021-10-01 17:23:21',
            ));

            
            

            Voyager::model('Permission')->generateFor('advertisers');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Advertisers',
                'url' => '',
                'route' => 'voyager.advertisers.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-group',
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
