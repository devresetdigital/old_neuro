<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class OrganizationsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'organizations')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'organizations')->delete();
            }

            \DB::table('data_types')->insert(array (
                'id' => 18,
                'name' => 'organizations',
                'slug' => 'organizations',
                'display_name_singular' => 'Organization',
                'display_name_plural' => 'Organizations',
                'icon' => 'voyager-people',
                'model_name' => 'App\\Organization',
                'policy_name' => NULL,
                'controller' => '\\App\\Http\\Controllers\\Voyager\\OrganizationController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"id","order_display_column":null}',
                'created_at' => '2018-09-07 17:35:31',
                'updated_at' => '2021-10-01 17:27:18',
            ));

            
            

            Voyager::model('Permission')->generateFor('organizations');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Organizations',
                'url' => '',
                'route' => 'voyager.organizations.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'voyager-people',
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
