<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnumValuesProjectPhase extends Migration
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
            [              
                'type'        => 'project_phase', 
                'subtype'     => 'prospect', 
                'key'         => 'pre_sales', 
                'value'       => 'PRE-SALES PHASE', 
                'description' => 'Project phase for prospect project stage.', 
                'active'      => 1, 
                'sortindex'   => 1, 
                'changed_by'  => 728
            ], [
                'type'        => 'project_phase', 
                'subtype'     => 'new_implementation', 
                'key'         => 'customization', 
                'value'       => 'CUSTOMIZATOIN PHASE', 
                'description' => 'Project phase for new implementation project stage.', 
                'active'      => 1, 
                'sortindex'   => 2, 
                'changed_by'  => 728
            ], [
                'type'        => 'project_phase', 
                'subtype'     => 'new_implementation', 
                'key'         => 'production', 
                'value'       => 'PRODUCTION PHASE', 
                'description' => 'Project phase for new implementation project stage.', 
                'active'      => 1, 
                'sortindex'   => 3, 
                'changed_by'  => 728
            ], [
                'type'        => 'project_phase', 
                'subtype'     => 'new_implementation', 
                'key'         => 'specification', 
                'value'       => 'SPECIFICATION PHASE', 
                'description' => 'Project phase for new implementation project stage.', 
                'active'      => 1, 
                'sortindex'   => 4, 
                'changed_by'  => 728
            ], [
                'type'        => 'project_phase', 
                'subtype'     => 'new_implementation', 
                'key'         => 'uat', 
                'value'       => 'UAT PHASE', 
                'description' => 'Project phase for new implementation project stage.', 
                'active'      => 1, 
                'sortindex'   => 5, 
                'changed_by'  => 728
            ], [
                'type'        => 'project_phase', 
                'subtype'     => 'released_on_prod', 
                'key'         => 'maintenance_support', 
                'value'       => 'MAINTENANCE & SUPPORT PHASE', 
                'description' => 'Project phase for released on prod project stage.', 
                'active'      => 1, 
                'sortindex'   => 6, 
                'changed_by'  => 728
            ]          
        ]
            );

            // mysql> select * from enum_values where type='project_phase';
            // +-----+---------------+--------------------+---------------------+-----------------------------+-----------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
            // | id  | type          | subtype            | key                 | value                       | description                                         | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
            // +-----+---------------+--------------------+---------------------+-----------------------------+-----------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
            // | 657 | project_phase | prospect           | pre_sales           | PRE-SALES PHASE             | Project phase for prospect project stage.           | NULL |      1 |         1 | NULL           | 2021-12-21 17:34:31 |        728 | none        |
            // | 658 | project_phase | new_implementation | customization       | CUSTOMIZATOIN PHASE         | Project phase for new implementation project stage. | NULL |      1 |         2 | NULL           | 2021-12-21 17:37:30 |        728 | none        |
            // | 659 | project_phase | new_implementation | production          | PRODUCTION PHASE            | Project phase for new implementation project stage. | NULL |      1 |         3 | NULL           | 2021-12-21 17:38:39 |        728 | none        |
            // | 660 | project_phase | new_implementation | specification       | SPECIFICATION PHASE         | Project phase for new implementation project stage. | NULL |      1 |         4 | NULL           | 2021-12-21 17:39:45 |        728 | none        |
            // | 661 | project_phase | new_implementation | uat                 | UAT PHASE                   | Project phase for new implementation project stage. | NULL |      1 |         5 | NULL           | 2021-12-21 17:41:35 |        728 | none        |
            // | 662 | project_phase | released_on_prod   | maintenance_support | MAINTENANCE & SUPPORT PHASE | Project phase for released on prod project stage.   | NULL |      1 |         6 | NULL           | 2021-12-21 17:52:33 |        728 | none        |
            // +-----+---------------+--------------------+---------------------+-----------------------------+-----------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
                
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('enum_values')->where('type', 'project_phase')->delete();
    }
}
