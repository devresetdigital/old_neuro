<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class CampaignsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'campaigns')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'campaigns')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 19,
                'name' => 'campaigns',
                'slug' => 'campaigns',
                'display_name_singular' => 'Campaign',
                'display_name_plural' => 'Campaigns',
                'icon' => 'voyager-megaphone',
                'model_name' => 'App\\Campaign',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\CampaignController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"desc","default_search_key":null,"scope":null}',
                'created_at' => '2018-09-07 23:41:50',
                'updated_at' => '2021-05-06 14:31:40',
            ));

            
            

            Voyager::model('Permission')->generateFor('campaigns');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Campaigns',
                'url' => '',
                'route' => 'voyager.campaigns.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-megaphone',
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
