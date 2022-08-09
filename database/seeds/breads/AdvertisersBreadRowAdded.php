<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class AdvertisersBreadRowAdded extends Seeder
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

            \DB::table('data_rows')->insert(array (
                0 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'id',
                    'type' => 'text',
                    'display_name' => 'id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => 'null',
                    'order' => 1,
                ),
                1 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'organization_id',
                    'type' => 'text',
                    'display_name' => 'Organization',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => 'null',
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
                    'details' => '"{\\"display\\":{\\"width\\":\\"3\\"}}"',
                    'order' => 3,
                ),
                3 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'domain',
                    'type' => 'text',
                    'display_name' => 'Domain',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"3\\"}}"',
                    'order' => 4,
                ),
                4 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'data_sharing',
                    'type' => 'checkbox',
                    'display_name' => 'Data Sharing',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{\\"0\\":\\"No\\",\\"1\\":\\"Yes\\",\\"checked\\":true}"',
                    'order' => 5,
                ),
                5 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'status',
                    'type' => 'checkbox',
                    'display_name' => 'Status',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 0,
                    'details' => '"{\\"0\\":\\"Inactive\\",\\"1\\":\\"Active\\",\\"checked\\":true}"',
                    'order' => 6,
                ),
                6 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'margin',
                    'type' => 'percentage',
                    'display_name' => 'Margin',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"2\\"},\\"validation\\":{\\"rule\\":\\"required|numeric\\",\\"messages\\":{\\"required\\":\\"This :attribute field is required.\\",\\"numeric\\":\\"This field must be a Number.\\"}}}"',
                    'order' => 7,
                ),
                7 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'advertiser_belongsto_organization_relationship',
                    'type' => 'relationship',
                    'display_name' => 'organizations',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"model\\":\\"App\\\\\\\\Organization\\",\\"table\\":\\"organizations\\",\\"type\\":\\"belongsTo\\",\\"column\\":\\"organization_id\\",\\"key\\":\\"id\\",\\"label\\":\\"name\\",\\"pivot_table\\":\\"advertisers\\",\\"pivot\\":\\"0\\",\\"taggable\\":\\"0\\"}"',
                    'order' => 8,
                ),
                8 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created At',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => '"{\\"format\\":\\"%d-%m-%Y\\"}"',
                    'order' => 9,
                ),
                9 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'updated_at',
                    'type' => 'timestamp',
                    'display_name' => 'Updated At',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 10,
                ),
                10 => 
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
                    'order' => 11,
                ),
            ));
        } catch(Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}

