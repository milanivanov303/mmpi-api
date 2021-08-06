<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnumValuesProjectStage extends Migration
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
                'type'        => 'project_stage', 
                'key'         => 'prospect', 
                'value'       => 'Prospect', 
                'description' => 'Prospect project stage', 
                'active'      => 1, 
                'sortindex'   => 1, 
                'changed_by'   => 728
            ], [
                'type'        => 'project_stage', 
                'key'         => 'new_implementation', 
                'value'       => 'New Implementation', 
                'description' => 'New Implementation project stage.', 
                'active'      => 1, 
                'sortindex'   => 2, 
                'changed_by'   => 728
            ], [
                'type'        => 'project_stage',
                'key'         => 'released_on_prod', 
                'value'       => 'Released on PROD', 
                'description' => 'Released on PROD project stage.', 
                'active'      => 1, 
                'sortindex'   => 3, 
                'changed_by'   => 728
            ], [
                'type'        => 'project_stage',
                'key'         => 'not_applicable', 
                'value'       => 'Not applicable', 
                'description' => 'Used for some internal projects or dummy MMPI projects in order to exclude them when needed.', 
                'active'      => 1, 
                'sortindex'   => 4, 
                'changed_by'   => 728
            ]          
        ]
            );

//mysql> select * from enum_values where type='project_stage';
//+-----+---------------+---------+--------------------+--------------------+----------------------------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| id  | type          | subtype | key                | value              | description                                                                                  | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
//+-----+---------------+---------+--------------------+--------------------+----------------------------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| 608 | project_stage | NULL    | prospect           | Prospect           | Prospect project stage                                                                       | NULL |      1 |         1 | NULL           | 2021-04-19 14:21:40 |        728 | none        |
//| 609 | project_stage | NULL    | new_implementation | New Implementation | New Implementation project stage.                                                            | NULL |      1 |         2 | NULL           | 2021-04-19 14:23:03 |        728 | none        |
//| 610 | project_stage | NULL    | released_on_prod   | Released on PROD   | Released on PROD project stage.                                                              | NULL |      1 |         3 | NULL           | 2021-04-19 14:23:53 |        728 | none        |
//| 611 | project_stage | NULL    | not_applicable     | Not applicable     | Used for some internal projects or dummy MMPI projects in order to exclude them when needed. | NULL |      1 |         4 | NULL           | 2021-04-20 11:19:48 |        728 | none        |
//+-----+---------------+---------+--------------------+--------------------+----------------------------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
  
        //add coulumn project_stage in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('project_stage') //Note: When creating a foreign key that references an incrementing integer, remember to always make the foreign key column unsigned.
                  ->nullable()
                  ->comment('The current stage of project.')
                  ->after('project_type');
            
            $table->foreign('project_stage')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');

            $table->index('project_stage');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('project_stage')
                  ->nullable()
                  ->comment('The current stage of project.')
                  ->after('project_type');    
        });

}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function($table){
            $table->dropForeign('projects_project_stage_foreign');
            $table->dropIndex('projects_project_stage_index');
            $table->dropColumn('project_stage');
        });

        Schema::table('projects_history', function($table){
            $table->dropColumn('project_stage');
        });

        DB::table('enum_values')->where('type', 'project_stage')->delete();
    }
}
