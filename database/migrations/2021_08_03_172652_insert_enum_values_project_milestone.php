<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnumValuesProjectMilestone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('enum_values')->insert(
            [
                'type'        => 'project_milestone',
                'key'         => 'proposal_del',
                'value'       => 'Contract Proposal Delivery', 
                'description' => 'Important date - Contract Proposal Delivery.',
                'active'      =>  1,
                'sortindex'   =>  1,
                'changed_by'  => 728
            ], [
                'type'        => 'project_milestone', 
                'key'         => 'signature_date', 
                'value'       => 'Contract Signature Date', 
                'description' => 'Important date - contract signature date.', 
                'active'      =>  1, 
                'sortindex'   =>  2,
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'client_kick_off', 
                'value'       => 'Client kick off', 
                'description' => 'Important date - Client Kick off meeting and products walk-through.', 
                'active'      =>  1, 'sortindex' =>  3, 'changed_by' => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'dac_signature', 
                'value'       => 'Validation of Specifications', 
                'description' => 'Important date - Validation of Specifications (DAC signature).', 
                'active'      =>  1,
                'sortindex'   =>  4,
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'internal_kick_off',
                'value'       => 'Internal kick off',
                'description' => 'Important date - Internal  Kick off meeting and products walk-through.', 
                'active'      =>  1, 
                'sortindex'   =>  5, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'apvl_book', 
                'value'       => 'Approval book validation by Client', 
                'description' => 'Important date - Approval book validation by Client.', 
                'active'      =>  1, 
                'sortindex'   =>  6, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'pre_validation', 
                'value'       => 'Pre-validation of the system', 
                'description' => 'Important date - Pre-validation of the system', 
                'active'      =>  1,
                'sortindex'   =>  7, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'test_cards', 
                'value'       => 'Test Cards Delivery', 
                'description' => 'Important date - Test Cards Delivery.', 
                'active'      =>  1, 
                'sortindex'   =>  8, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'final_delivery', 
                'value'       => 'Final Delivery of the solution before GO-LIVE on PROD', 
                'description' => 'Important date - Final Delivery of the solution before GO-LIVE on PROD.', 
                'active'      =>  1, 
                'sortindex'   =>  9, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'final_user_acceptance', 
                'value'       => 'Production Roll-out Green Light (Final User Acceptance)', 
                'description' => 'Important date - Production Roll-out Green Light (Final User Acceptance). ', 
                'active'      =>  1, 
                'sortindex'   => 10, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'go_live', 
                'value'       => 'Project GO-LIVE on PROD', 
                'description' => 'Important date - Project GO-LIVE on PROD.', 
                'active'      =>  1, 'sortindex' => 11, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'release_delivery', 
                'value'       => 'Release delivery on PROD', 
                'description' => 'Important date - Release delivery on PROD.', 
                'active'      =>  1, 
                'sortindex'   => 12, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'stop_full_del_AD', 
                'value'       => 'Stop full deliveries date - AD', 
                'description' => 'Important date - Stop full deliveries date - AD.', 
                'active'      =>  1, 'sortindex' => 13, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'stop_full_del_V9', 
                'value'       => 'Stop full deliveries date - V9', 
                'description' => 'Important date - Stop full deliveries date - V9.', 
                'active'      =>  1, 
                'sortindex'   => 14, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone',     
                'key'         => 'stop_full_del_XNET', 
                'value'       => 'Stop full deliveries date - XNET', 
                'description' => 'Important date - Stop full deliveries date - XNET .', 
                'active'      =>  1, 
                'sortindex'   => 15, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'dev_deploy', 
                'value'       => 'DEV Instance ready for development', 
                'description' => 'Important date - DEV Instance ready for development.', 
                'active'      =>  1, 
                'sortindex'   => 16, 
                'changed_by ' => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'val_use', 
                'value'       => 'VAL instance ready for use', 
                'description' => 'Important date - VAL instance ready for use.', 
                'active'      =>  1, 
                'sortindex'   => 17, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'uat_use', 
                'value'       => 'UAT instance ready for use', 
                'description' => 'Important date - UAT instance ready for use.', 
                'active'      =>  1, 
                'sortindex'   => 18, 
                'changed_by'  => 728
            ], [ 
                'type'        => 'project_milestone', 
                'key'         => 'prod_use' , 
                'value'       => 'PROD instance ready for use', 
                'description' => 'Important date - PROD instance ready for use.', 
                'active'      =>  1, 
                'sortindex'   => 19, 
                'changed_by'  => 728
            ]
         );

// mysql> select * from enum_values where type='project_milestone';
// +-----+-------------------+---------+-----------------------+---------------------------------------------------------+---------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
// | id  | type              | subtype | key                   | value                                                   | description                                                               | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
// +-----+-------------------+---------+-----------------------+---------------------------------------------------------+---------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
// | 628 | project_milestone | NULL    | proposal_del          | Contract Proposal Delivery                              | Important date - Contract Proposal Delivery.                              | NULL |      1 |         1 | NULL           | 2021-07-20 16:48:38 |        728 | none        |
// | 629 | project_milestone | NULL    | signature_date        | Contract Signature Date                                 | Important date - contract signature date.                                 | NULL |      1 |         2 | NULL           | 2021-07-20 16:50:32 |        728 | none        |
// | 630 | project_milestone | NULL    | client_kick_off       | Client kick off                                         | Important date - Client Kick off meeting and products walk-through.       | NULL |      1 |         3 | NULL           | 2021-07-20 16:53:58 |        728 | none        |
// | 631 | project_milestone | NULL    | dac_signature         | Validation of Specifications                            | Important date - Validation of Specifications (DAC signature).            | NULL |      1 |         4 | NULL           | 2021-07-20 16:55:26 |        728 | none        |
// | 632 | project_milestone | NULL    | internal_kick_off     | Internal kick off                                       | Important date - Internal  Kick off meeting and products walk-through.    | NULL |      1 |         5 | NULL           | 2021-07-20 16:57:34 |        728 | none        |
// | 633 | project_milestone | NULL    | apvl_book             | Approval book validation by Client                      | Important date - Approval book validation by Client.                      | NULL |      1 |         6 | NULL           | 2021-07-20 17:01:05 |        728 | none        |
// | 634 | project_milestone | NULL    | pre_validation        | Pre-validation of the system                            | Important date - Pre-validation of the system                             | NULL |      1 |         7 | NULL           | 2021-07-20 17:02:02 |        728 | none        |
// | 635 | project_milestone | NULL    | test_cards            | Test Cards Delivery                                     | Important date - Test Cards Delivery.                                     | NULL |      1 |         8 | NULL           | 2021-07-20 17:04:30 |        728 | none        |
// | 636 | project_milestone | NULL    | final_delivery        | Final Delivery of the solution before GO-LIVE on PROD   | Important date - Final Delivery of the solution before GO-LIVE on PROD.   | NULL |      1 |         9 | NULL           | 2021-07-20 17:06:03 |        728 | none        |
// | 637 | project_milestone | NULL    | final_user_acceptance | Production Roll-out Green Light (Final User Acceptance) | Important date - Production Roll-out Green Light (Final User Acceptance). | NULL |      1 |        10 | NULL           | 2021-07-20 17:07:50 |        728 | none        |
// | 638 | project_milestone | NULL    | go_live               | Project GO-LIVE on PROD                                 | Important date - Project GO-LIVE on PROD.                                 | NULL |      1 |        11 | NULL           | 2021-07-20 17:08:30 |        728 | none        |
// | 639 | project_milestone | NULL    | release_delivery      | Release delivery on PROD                                | Important date - Release delivery on PROD.                                | NULL |      1 |        12 | NULL           | 2021-07-20 17:09:10 |        728 | none        |
// | 640 | project_milestone | NULL    | stop_full_del_AD      | Stop full deliveries date - AD                          | Important date - Stop full deliveries date - AD.                          | NULL |      1 |        13 | NULL           | 2021-07-20 17:10:06 |        728 | none        |
// | 641 | project_milestone | NULL    | stop_full_del_V9      | Stop full deliveries date - V9                          | Important date - Stop full deliveries date - V9.                          | NULL |      1 |        14 | NULL           | 2021-07-20 17:11:12 |        728 | none        |
// | 642 | project_milestone | NULL    | stop_full_del_XNET    | Stop full deliveries date - XNET                        | Important date - Stop full deliveries date - XNET .                       | NULL |      1 |        15 | NULL           | 2021-07-20 17:11:56 |        728 | none        |
// | 643 | project_milestone | NULL    | dev_deploy            | DEV Instance ready for development                      | Important date - DEV Instance ready for development.                      | NULL |      1 |        16 | NULL           | 2021-07-20 17:12:40 |        728 | none        |
// | 644 | project_milestone | NULL    | val_use               | VAL instance ready for use                              | Important date - VAL instance ready for use.                              | NULL |      1 |        17 | NULL           | 2021-07-20 17:13:22 |        728 | none        |
// | 645 | project_milestone | NULL    | uat_use               | UAT instance ready for use                              | Important date - UAT instance ready for use.                              | NULL |      1 |        18 | NULL           | 2021-07-20 17:14:00 |        728 | none        |
// | 646 | project_milestone | NULL    | prod_use              | PROD instance ready for use                             | Important date - PROD instance ready for use.                             | NULL |      1 |        19 | NULL           | 2021-07-20 17:14:49 |        728 | none        |
// +-----+-------------------+---------+-----------------------+---------------------------------------------------------+---------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('enum_values')->where('type', 'project_milestone')->delete();
    }
}
