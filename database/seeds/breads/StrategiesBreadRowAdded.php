<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class StrategiesBreadRowAdded extends Seeder
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

            $dataType = DataType::where('name', 'strategies')->first();

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
                    'field' => 'status',
                    'type' => 'checkbox',
                    'display_name' => 'Status',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"on\\":\\"Active\\",\\"off\\":\\"Inactive\\",\\"checked\\":true,\\"display\\":{\\"width\\":\\"2\\"}}"',
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
                    'details' => '"{\\"display\\":{\\"width\\":\\"10\\"},\\"style\\":{\\"max-width\\":\\"50%\\"}}"',
                    'order' => 3,
                ),
                3 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'campaign_id',
                    'type' => 'hidden',
                    'display_name' => 'Campaign Id',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{\\"display\\":{\\"width\\":\\"5\\"}}"',
                    'order' => 4,
                ),
                4 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'strategy_belongsto_campaign_relationship',
                    'type' => 'relationship',
                    'display_name' => 'campaigns',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => '"{\\"model\\":\\"App\\\\\\\\Campaign\\",\\"table\\":\\"campaigns\\",\\"type\\":\\"belongsTo\\",\\"column\\":\\"campaign_id\\",\\"key\\":\\"id\\",\\"label\\":\\"name\\",\\"pivot_table\\":\\"advertisers\\",\\"pivot\\":\\"0\\",\\"taggable\\":\\"0\\"}"',
                    'order' => 5,
                ),
                5 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'channel',
                    'type' => 'select_dropdown',
                    'display_name' => 'Channel',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{\\"default\\":\\"1\\",\\"options\\":{\\"1\\":\\"Display\\",\\"2\\":\\"Video\\/Audio\\"},\\"display\\":{\\"width\\":\\"3\\"}}"',
                    'order' => 6,
                ),
                6 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'date_start',
                    'type' => 'date',
                    'display_name' => 'Start',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"3\\"}}"',
                    'order' => 7,
                ),
                7 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'date_end',
                    'type' => 'date',
                    'display_name' => 'End',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"9\\"},\\"style\\":{\\"max-width\\":\\"21.5em\\"}}"',
                    'order' => 8,
                ),
                8 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'budget',
                    'type' => 'number',
                    'display_name' => 'Budget',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"numeric\\",\\"messages\\":{\\"numeric\\":\\"This field must be numeric and can not contain comas\\"}},\\"display\\":{\\"width\\":\\"12\\"},\\"extra\\":{\\"prefix\\":\\"$\\"},\\"style\\":{\\"max-width\\":\\"21%\\"}}"',
                    'order' => 9,
                ),
                9 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'goal_type',
                    'type' => 'hidden',
                    'display_name' => 'Goal Type',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"default\\":\\"5\\",\\"options\\":{\\"1\\":\\"CPC\\",\\"2\\":\\"CTR\\",\\"3\\":\\"Viewability Rate\\",\\"4\\":\\"Viewable CPM\\",\\"5\\":\\"CPM REACH\\"},\\"display\\":{\\"width\\":\\"0\\"}}"',
                    'order' => 10,
                ),
                10 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'goal_values',
                    'type' => 'text',
                    'display_name' => 'Strategy Goals',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"12\\"}}"',
                    'order' => 11,
                ),
                11 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'pacing_monetary',
                    'type' => 'number',
                    'display_name' => 'Pacing Monetary',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"12\\"}}"',
                    'order' => 12,
                ),
                12 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'pacing_impression',
                    'type' => 'number',
                    'display_name' => 'Pacing Impression',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"12\\"}}"',
                    'order' => 13,
                ),
                13 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'frequency_cap',
                    'type' => 'text',
                    'display_name' => 'Frequency Cap',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"12\\"}}"',
                    'order' => 14,
                ),
                14 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created At',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 15,
                ),
                15 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'updated_at',
                    'type' => 'timestamp',
                    'display_name' => 'Updated At',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => 'null',
                    'order' => 16,
                ),
                16 => 
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
                    'order' => 17,
                ),
                17 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'checked',
                    'type' => 'text',
                    'display_name' => 'Checked',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => 'null',
                    'order' => 18,
                ),
                18 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'bgcheck',
                    'type' => 'text',
                    'display_name' => 'Bgcheck',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => 'null',
                    'order' => 18,
                ),
            ));
        } catch(Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}

