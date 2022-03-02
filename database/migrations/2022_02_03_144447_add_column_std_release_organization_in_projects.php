<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStdReleaseOrganizationInProjects extends Migration
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
                           'type'        => 'project_specific_feature', 
                           'subtype'     => 'std_release_organization', 
                           'key'         => 'r_org_yes', 
                           'value'       => 'Yes', 
                           'description' => 'Keep developer as assignee of CVSHEAD tasks.', 
                           'active'      => 1, 
                           'sortindex'   => $max_sortindex->sortindex + 1, 
                           'changed_by'  => 728 
                       ], [ 
                           'type'        => 'project_specific_feature', 
                           'subtype'     => 'std_release_organization', 
                           'key'         => 'r_org_no', 
                           'value'       => 'No', 
                           'description' => 'Use PC of the project as assignee of CVSHEAD tasks.', 
                           'active'      => 1, 
                           'sortindex'   => $max_sortindex->sortindex + 2, 
                           'changed_by'  => 728 
                       ]    
                   ]
               );

        
        //add column std_release_organization in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('std_release_organization')
                  ->nullable()
                  ->comment('Flag for organization of client deliveries related to CVSHEAD tasks. Yes/NULL - keep developer as assignee; No - use PC as assignee.')
                  ->after('v_menu_mntd_by_clnt_id');
            
            $table->foreign('std_release_organization')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');
            $table->index('std_release_organization');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('std_release_organization')
                  ->nullable()
                  ->comment('Flag for organization of client deliveries related to CVSHEAD tasks. Yes/NULL - keep developer as assignee; No - use PC as assignee.')
                  ->after('v_menu_mntd_by_clnt_id');    
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
            $table->dropForeign('projects_std_release_organization_foreign');
            $table->dropIndex('projects_std_release_organization_index');
            $table->dropColumn('std_release_organization');
        });

        Schema::table('projects_history', function($table){
            $table->dropColumn('std_release_organization');
        });

        DB::table('enum_values')
        ->where('type', 'project_specific_feature')
        ->where('subtype', 'std_release_organization')
        ->delete();
    }
}
