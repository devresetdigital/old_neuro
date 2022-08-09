<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class BlocklistsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'blacklists')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'blacklists')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 17,
                'name' => 'blacklists',
                'slug' => 'blocklists',
                'display_name_singular' => 'Blocklist',
                'display_name_plural' => 'Blocklists',
                'icon' => 'voyager-list',
                'model_name' => 'App\\Blacklist',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\BlacklistsController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":null,"order_display_column":null}',
                'created_at' => '2018-09-07 16:37:22',
                'updated_at' => '2021-04-06 14:57:19',
            ));

            
            

            Voyager::model('Permission')->generateFor('blacklists');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Blocklists',
                'url' => '',
                'route' => 'voyager.blocklists.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-list',
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
