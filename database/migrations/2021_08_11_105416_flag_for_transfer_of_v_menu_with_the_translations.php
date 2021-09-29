<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlagForTransferOfVMenuWithTheTranslations extends Migration
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
                                        'subtype'     => 'v_menu_mntd_by_codix', 
                                        'key'         => 'v_menu_yes', 
                                        'value'       => 'Yes', 
                                        'description' => 'V_MENU and TSS_MENU are maintained by Codix.', 
                                        'active'      => 1, 
                                        'sortindex'   => $max_sortindex->sortindex + 1, 
                                        'changed_by'  => 728 
                                    ], [ 
                                        'type'        => 'project_specific_feature', 
                                        'subtype'     => 'v_menu_mntd_by_codix', 
                                        'key'         => 'v_menu_no', 
                                        'value'       => 'No', 
                                        'description' => 'V_MENU and TSS_MENU are maintained by Client.', 
                                        'active'      => 1, 
                                        'sortindex'   => $max_sortindex->sortindex + 2, 
                                        'changed_by'  => 728 
                                    ]    
                                ]
                            );
//+-----+--------------------------+----------------------+------------+-------+----------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| id  | type                     | subtype              | key        | value | description                                  | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
//+-----+--------------------------+----------------------+------------+-------+----------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| 647 | project_specific_feature | v_menu_mntd_by_codix | v_menu_yes | Yes   | V_MENU and TSS_MENU are maintained by Codix. | NULL |      1 |       427 | NULL           | 2021-07-28 17:28:58 |        728 | none        |
//| 648 | project_specific_feature | v_menu_mntd_by_codix | v_menu_no  | No    | V_MENU and TSS_MENU are maintained by Client | NULL |      1 |       428 | NULL           | 2021-07-28 17:30:32 |        728 | none        |
//+-----+--------------------------+----------------------+------------+-------+----------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+                                                         

        //add column v_menu_mntd_by_clnt_id in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('v_menu_mntd_by_clnt_id')
                  ->nullable()
                  ->comment('If V_MENU and T_SSMENU is maintained by the clinet or codix. Controls "Menu and Translation Transfer" tab.')
                  ->after('trans_mntd_by_clnt_id');
            
            $table->foreign('v_menu_mntd_by_clnt_id')
                  ->references('id')
                  ->on('enum_values')
                  ->onUpdate('cascade');
            $table->index('v_menu_mntd_by_clnt_id');
            
        });

        //add the same column in projects_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('v_menu_mntd_by_clnt_id')
                  ->nullable()
                  ->comment('If V_MENU and T_SSMENU is maintained by the clinet or codix. Controls "Menu and Translation Transfer" tab.')
                  ->after('trans_mntd_by_clnt_id');    
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
            $table->dropForeign('projects_v_menu_mntd_by_clnt_id_foreign');
            $table->dropIndex('projects_v_menu_mntd_by_clnt_id_index');
            $table->dropColumn('v_menu_mntd_by_clnt_id');
        });

        Schema::table('projects_history', function($table){
            $table->dropColumn('v_menu_mntd_by_clnt_id');
        });

        DB::table('enum_values')
        ->where('type', 'project_specific_feature')
        ->where('subtype', 'v_menu_mntd_by_codix')
        ->delete();
    }
}
