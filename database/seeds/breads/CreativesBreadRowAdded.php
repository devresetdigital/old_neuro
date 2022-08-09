<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class CreativesBreadRowAdded extends Seeder
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

            \DB::table('data_rows')->insert(array (
                0 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'id',
                    'type' => 'text',
                    'display_name' => 'Id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{\\"display\\":{\\"width\\":\\"3\\"}}"',
                    'order' => 1,
                ),
                1 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'creative_type_id',
                    'type' => 'select_dropdown',
                    'display_name' => 'Creative Type',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"default\\":\\"1\\",\\"options\\":{\\"1\\":\\"Display\\",\\"2\\":\\"Video\\",\\"3\\":\\"Audio\\"},\\"display\\":{\\"width\\":\\"4\\"}}"',
                    'order' => 2,
                ),
                2 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'name',
                    'type' => 'text',
                    'display_name' => 'Name',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"4\\"}}"',
                    'order' => 3,
                ),
                3 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'status',
                    'type' => 'checkbox',
                    'display_name' => 'Status',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"on\\":\\"Active\\",\\"off\\":\\"Inactive\\",\\"checked\\":\\"false\\",\\"display\\":{\\"width\\":\\"4\\"}}"',
                    'order' => 4,
                ),
                4 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'advertiser_id',
                    'type' => 'text',
                    'display_name' => 'Advertisers',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 0,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}},\\"display\\":{\\"width\\":\\"6\\"}}"',
                    'order' => 5,
                ),
                5 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'concept_id',
                    'type' => 'text',
                    'display_name' => 'Concepts',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 0,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}},\\"display\\":{\\"width\\":\\"6\\"}}"',
                    'order' => 6,
                ),
                6 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'secure',
                    'type' => 'checkbox',
                    'display_name' => 'Secure',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{\\"on\\":\\"Secure\\",\\"off\\":\\"Non Secure\\",\\"checked\\":\\"true\\",\\"display\\":{\\"width\\":\\"3\\"}}"',
                    'order' => 7,
                ),
                7 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'click_url',
                    'type' => 'text',
                    'display_name' => 'Click Url',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"6\\"}}"',
                    'order' => 8,
                ),
                8 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => '3pas_tag_id',
                    'type' => 'text',
                    'display_name' => '3pas Tag Id',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{\\"display\\":{\\"width\\":\\"4\\"}}"',
                    'order' => 9,
                ),
                9 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'landing_page',
                    'type' => 'text',
                    'display_name' => 'Landing Page',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}},\\"display\\":{\\"width\\":\\"6\\"}}"',
                    'order' => 10,
                ),
                10 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'start_date',
                    'type' => 'date',
                    'display_name' => 'Start Date',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"4\\"}}"',
                    'order' => 11,
                ),
                11 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'end_date',
                    'type' => 'date',
                    'display_name' => 'End Date',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"4\\"}}"',
                    'order' => 12,
                ),
                12 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => '"{\\"format\\":\\"%m-%d-%Y\\"}"',
                    'order' => 13,
                ),
                13 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'updated_at',
                    'type' => 'timestamp',
                    'display_name' => 'Updated At',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => 'null',
                    'order' => 14,
                ),
                14 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'deleted_at',
                    'type' => 'timestamp',
                    'display_name' => 'Deleted At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => 'null',
                    'order' => 15,
                ),
            ));
        } catch(Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}

