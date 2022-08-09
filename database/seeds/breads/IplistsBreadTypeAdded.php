<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class IplistsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'iplists')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'iplists')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 33,
                'name' => 'iplists',
                'slug' => 'iplists',
                'display_name_singular' => 'Iplist',
                'display_name_plural' => 'Iplists',
                'icon' => NULL,
                'model_name' => 'App\\Iplist',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\IplistController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2019-11-28 17:49:15',
                'updated_at' => '2020-10-22 16:05:43',
            ));

            
            

            Voyager::model('Permission')->generateFor('iplists');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Iplists',
                'url' => '',
                'route' => 'voyager.iplists.index',
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
