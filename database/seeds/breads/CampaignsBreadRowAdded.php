<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class CampaignsBreadRowAdded extends Seeder
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

            \DB::table('data_rows')->insert(array (
                0 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'id',
                    'type' => 'text',
                    'display_name' => 'Id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{}"',
                    'order' => 1,
                ),
                1 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'user_id',
                    'type' => 'text',
                    'display_name' => 'User Id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}}}"',
                    'order' => 2,
                ),
                2 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'advertiser_id',
                    'type' => 'text',
                    'display_name' => 'Advertiser Id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"validation\\":{\\"rule\\":\\"required\\",\\"messages\\":{\\"required\\":\\"This field is required.\\"}},\\"display\\":{\\"width\\":\\"5\\"}}"',
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
                    'details' => '"{\\"on\\":\\"Active\\",\\"off\\":\\"Inactive\\",\\"checked\\":false,\\"display\\":{\\"width\\":\\"1\\"}}"',
                    'order' => 4,
                ),
                4 => 
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
                    'details' => '"{\\"display\\":{\\"width\\":\\"11\\"},\\"style\\":{\\"max-width\\":\\"50%\\"}}"',
                    'order' => 5,
                ),
                5 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'managed',
                    'type' => 'checkbox',
                    'display_name' => 'Managed',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"2\\"}}"',
                    'order' => 6,
                ),
                6 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'ad_collitions',
                    'type' => 'checkbox',
                    'display_name' => 'Ad Collitions',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"2\\"}}"',
                    'order' => 7,
                ),
                7 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'antifraud',
                    'type' => 'checkbox',
                    'display_name' => 'Anti fraud',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 0,
                    'details' => '"{\\"display\\":{\\"width\\":\\"2\\"}}"',
                    'order' => 8,
                ),
                8 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'goal_type_id',
                    'type' => 'select_dropdown',
                    'display_name' => 'Goal Type',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"default\\":\\"0\\",\\"options\\":{\\"0\\":\\"None\\",\\"1\\":\\"ROI\\",\\"2\\":\\"CPA\\",\\"3\\":\\"CPC\\",\\"4\\":\\"CTR\\",\\"5\\":\\"Video completition rate\\",\\"6\\":\\"Viewability Rate\\",\\"7\\":\\"Viewable CPM\\",\\"8\\":\\"CPM REACH\\",\\"9\\":\\"CPM SPEND\\"},\\"display\\":{\\"width\\":\\"5\\"}}"',
                    'order' => 9,
                ),
                9 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'goal_v1',
                    'type' => 'text',
                    'display_name' => 'Goal Value',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"2\\"}}"',
                    'order' => 10,
                ),
                10 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'goal_v2',
                    'type' => 'text',
                    'display_name' => 'Goal V2',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{}"',
                    'order' => 11,
                ),
                11 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'goal_v3',
                    'type' => 'text',
                    'display_name' => 'Goal V3',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '"{}"',
                    'order' => 12,
                ),
                12 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'pacing_monetary',
                    'type' => 'text',
                    'display_name' => 'Pacing Monetary',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"5\\"}}"',
                    'order' => 13,
                ),
                13 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'pacing_impression',
                    'type' => 'text',
                    'display_name' => 'Pacing Impression',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"display\\":{\\"width\\":\\"5\\"}}"',
                    'order' => 14,
                ),
                14 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => '"{\\"format\\":\\"%m-%d-%Y\\"}"',
                    'order' => 15,
                ),
                15 => 
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
                    'details' => '"{}"',
                    'order' => 16,
                ),
                16 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'campaign_belongsto_advertiser_relationship',
                    'type' => 'relationship',
                    'display_name' => 'Advertisers',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"model\\":\\"App\\\\\\\\Advertiser\\",\\"table\\":\\"advertisers\\",\\"type\\":\\"belongsTo\\",\\"column\\":\\"advertiser_id\\",\\"key\\":\\"id\\",\\"label\\":\\"name\\",\\"pivot_table\\":\\"advertisers\\",\\"pivot\\":\\"0\\",\\"taggable\\":\\"0\\"}"',
                    'order' => 17,
                ),
                17 => 
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
                    'details' => '"{}"',
                    'order' => 18,
                ),
                18 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'campaign_belongsto_user_relationship',
                    'type' => 'relationship',
                    'display_name' => 'Users',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '"{\\"model\\":\\"App\\\\\\\\User\\",\\"table\\":\\"users\\",\\"type\\":\\"belongsTo\\",\\"column\\":\\"user_id\\",\\"key\\":\\"id\\",\\"label\\":\\"email\\",\\"pivot_table\\":\\"advertisers\\",\\"pivot\\":\\"0\\",\\"taggable\\":\\"0\\"}"',
                    'order' => 19,
                ),
                19 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'vwis',
                    'type' => 'text',
                    'display_name' => 'Vwis',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => '"{}"',
                    'order' => 20,
                ),
                20 => 
                array (
                    'data_type_id' => $dataType->id,
                    'field' => 'vwis_location',
                    'type' => 'text',
                    'display_name' => 'Vwis Location',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => '"{}"',
                    'order' => 21,
                ),
            ));
        } catch(Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}

