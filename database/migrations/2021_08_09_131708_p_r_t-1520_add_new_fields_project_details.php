<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PRT1520AddNewFieldsProjectDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //get max sortindex by type
        $max_sortindex = DB::table('enum_values')                                                                                                                                                                                                                                                  
                             ->where('type','project_specific_feature')
                             ->orderBy('sortindex', 'desc')
                             ->first('sortindex');

        DB::table('enum_values')->insert(
            [                
                [
                    //support_service 
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'support_service', 
                    'key'         => 'full_support', 
                    'value'       => '24h/7d', 
                    'description' => 'Support Model Window - 24h/7d.', 
                    'active'      => 1, 
                    'sortindex'   => $max_sortindex->sortindex + 1,
                    'changed_by'  => 728
                ], [ 
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'support_service', 
                    'key'         => 'partial_support', 
                    'value'       => '5h/7d', 
                    'description' => 'Support Model Window - 5h/7d.', 
                    'active'      => 1, 
                    'sortindex'   => $max_sortindex->sortindex + 2,
                    'changed_by'  => 728
                ], [ 
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'support_service', 
                    'key'         => 'week_support', 
                    'value'       => '24h/5d', 
                    'description' => 'Support Model Window - 24h/5d', 
                    'active'      => 1, 
                    'sortindex'   => $max_sortindex->sortindex + 3, 
                    'changed_by'  => 728
                ], [
                    //implementation
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'implementation',
                    'key'         => 'on_permises',
                    'value'       => 'OnPermises',
                    'description' => 'Implementation feature of the project - on_permises software.', 
                    'active'      => 1, 
                    'sortindex'   => $max_sortindex->sortindex + 4, 
                    'changed_by'  => 728
                ], [
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'implementation', 
                    'key'         => 'saas', 
                    'value'       => 'SAAS', 
                    'description' => 'Implementation feature of the project - Software as a Service (SAAS).', 
                    'active'      => 1,
                    'sortindex'   => $max_sortindex->sortindex + 5, 
                    'changed_by'  => 728
                ], [
                    //night_job_execution
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'night_job_execution', 
                    'key'         => 'every_day', 
                    'value'       => 'Every Day', 
                    'description' => 'Night Job execution - supported every day.', 
                    'active'      => 1, 
                    'sortindex'   => $max_sortindex->sortindex + 6, 
                    'changed_by'  => 728
                ], [
                    'type'        => 'project_specific_feature', 
                    'subtype'     => 'night_job_execution', 
                    'key'         => 'working_days_only', 
                    'value'       => 'Working days only', 
                    'description' => 'Night Job execution - supported working days only.', 
                    'active'      => 1, 
                    'sortindex'   => $max_sortindex->sortindex + 7, 
                    'changed_by'  => 728
                ]                                      
            ]
        );

//+-----+--------------------------+---------------------+-------------------+-------------------+-----------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| id  | type                     | subtype             | key               | value             | description                                                           | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
//+-----+--------------------------+---------------------+-------------------+-------------------+-----------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| 616 | project_specific_feature | implementation      | on_permises       | OnPermises        | Implementation feature of the project - on_permises software.         | NULL |      1 |       421 | NULL           | 2021-07-16 11:29:33 |        728 | none        |
//| 615 | project_specific_feature | implementation      | saas              | SAAS              | Implementation feature of the project - Software as a Service (SAAS). | NULL |      1 |       420 | NULL           | 2021-07-16 11:24:19 |        728 | none        |
//| 617 | project_specific_feature | night_job_execution | every_day         | Every Day         | Night Job execution - supported every day.                            | NULL |      1 |       422 | NULL           | 2021-07-16 11:32:57 |        728 | none        |
//| 618 | project_specific_feature | night_job_execution | working_days_only | Working days only | Night Job execution - supported working days only.                    | NULL |      1 |       423 | NULL           | 2021-07-16 11:34:53 |        728 | none        |
//| 619 | project_specific_feature | support_service     | full_support      | 24h/7d            | Support Model Window - 24h/7d.                                        | NULL |      1 |       424 | NULL           | 2021-07-16 11:44:28 |        728 | none        |
//| 621 | project_specific_feature | support_service     | partial_support   | 5h/7d             | Support Model Window - 5h/7d.                                         | NULL |      1 |       426 | NULL           | 2021-07-16 11:58:15 |        728 | none        |
//| 620 | project_specific_feature | support_service     | week_support      | 24h/5d            | Support Model Window - 24h/5d                                         | NULL |      1 |       425 | NULL           | 2021-07-16 11:55:31 |        728 | none        |
//+-----+--------------------------+---------------------+-------------------+-------------------+-----------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+

        //add column support_service in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('support_service')
                  ->nullable()
                  ->comment('Support Model Window for project.')
                  ->after('project_stage');
            
            $table->foreign('support_service')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');
            $table->index('support_service');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('support_service')
                  ->nullable()
                  ->comment('Support Model Window for project.')
                  ->after('project_stage');    
        });

        //add column implementation in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('implementation')
                  ->nullable()
                  ->comment('Implementation feature of the project - SAAS/OnPermises.')
                  ->after('project_stage');
            
            $table->foreign('implementation')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');
            $table->index('implementation');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('implementation')
                  ->nullable()
                  ->comment('Implementation feature of the project - SAAS/OnPermises.')
                  ->after('project_stage');    
        });

        //add column night_job_execution in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('night_job_execution')
                  ->nullable()
                  ->comment('Night Job execution - every day, working days only.')
                  ->after('project_stage');
            
            $table->foreign('night_job_execution')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');
            $table->index('night_job_execution');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('night_job_execution')
                  ->nullable()
                  ->comment('Night Job execution - every day, working days only.')
                  ->after('project_stage');    
        });

        //add column app_date_used in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('app_date_used')
                  ->nullable()
                  ->comment('Application date used.')
                  ->after('project_stage');
            
            $table->foreign('app_date_used')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');
            $table->index('app_date_used');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('app_date_used')
                  ->nullable()
                  ->comment('Application date used.')
                  ->after('project_stage');    
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function($table)
        {
            $table->dropForeign('projects_support_service_foreign');
            $table->dropIndex('projects_support_service_index');
            $table->dropColumn('support_service');
        });

        Schema::table('projects', function($table)
        {
            $table->dropForeign('projects_implementation_foreign');
            $table->dropIndex('projects_implementation_index');
            $table->dropColumn('implementation');
        });

        Schema::table('projects', function($table)
        {
            $table->dropForeign('projects_night_job_execution_foreign');
            $table->dropIndex('projects_night_job_execution_index');
            $table->dropColumn('night_job_execution');
        });

        Schema::table('projects', function($table)
        {
            $table->dropForeign('projects_app_date_used_foreign');
            $table->dropIndex('projects_app_date_used_index');
            $table->dropColumn('app_date_used');
        });

        Schema::table('projects_history', function($table){
            $table->dropColumn('support_service');
            $table->dropColumn('implementation');
            $table->dropColumn('night_job_execution');
            $table->dropColumn('app_date_used');
        });

        DB::table('enum_values')
          ->where('type', 'project_specific_feature')
          ->whereIn('subtype', ['support_service', 'implementation', 'night_job_execution'])
          ->delete();
    }
}
