<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class PmpsBreadRowAdded extends Seeder
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

            $dataType = DataType::where('name', 'pmps')->first();

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
                    'details' => 'null',
                    'order' => 1,
                ),
                1 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'organization_id',
                    'type' => 'text',
                    'display_name' => 'Organization',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}}}"',
                    'order' => 2,
                ),
                2 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'ssp_id',
                    'type' => 'text',
                    'display_name' => 'Ssp',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}}}"',
                    'order' => 3,
                ),
                3 => 
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
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}}}"',
                    'order' => 4,
                ),
                4 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'deal_id',
                    'type' => 'text',
                    'display_name' => 'Deal Id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}}}"',
                    'order' => 5,
                ),
                5 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'price',
                    'type' => 'number',
                    'display_name' => 'Price',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 6,
                ),
                6 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'fixed',
                    'type' => 'checkbox',
                    'display_name' => 'Fixed',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"on\\":\\"Yes \\",\\"off\\":\\"No\\",\\"checked\\":false}"',
                    'order' => 7,
                ),
                7 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'description',
                    'type' => 'text_area',
                    'display_name' => 'Description',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 8,
                ),
                8 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'date_start',
                    'type' => 'timestamp',
                    'display_name' => 'Date Start',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 9,
                ),
                9 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'date_end',
                    'type' => 'timestamp',
                    'display_name' => 'Date End',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 10,
                ),
                10 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 0,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 11,
                ),
                11 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'updated_at',
                    'type' => 'timestamp',
                    'display_name' => 'Updated At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => 'null',
                    'order' => 12,
                ),
                12 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'pmp_belongsto_ssp_relationship',
                    'type' => 'relationship',
                    'display_name' => 'ssps',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"model\\":\\"App\\\\\\\\Ssp\\",\\"table\\":\\"ssps\\",\\"type\\":\\"belongsTo\\",\\"column\\":\\"ssp_id\\",\\"key\\":\\"id\\",\\"label\\":\\"name\\",\\"pivot_table\\":\\"advertisers\\",\\"pivot\\":\\"0\\",\\"taggable\\":\\"0\\"}"',
                    'order' => 13,
                ),
                13 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'pmp_belongsto_organization_relationship',
                    'type' => 'relationship',
                    'display_name' => 'organizations',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"model\\":\\"App\\\\\\\\Organization\\",\\"table\\":\\"organizations\\",\\"type\\":\\"belongsTo\\",\\"column\\":\\"organization_id\\",\\"key\\":\\"id\\",\\"label\\":\\"name\\",\\"pivot_table\\":\\"advertisers\\",\\"pivot\\":\\"0\\",\\"taggable\\":\\"0\\"}"',
                    'order' => 14,
                ),
            ));
        } catch(Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}

