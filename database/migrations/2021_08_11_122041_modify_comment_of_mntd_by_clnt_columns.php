<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCommentOfMntdByClntColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //change the coment of some columns
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('se_mntd_by_clnt_id')
                  ->comment('If Expert System is maintained by the clinet or codix.')
                  ->change();
            $table->integer('tl_mntd_by_clnt_id')
                  ->comment('If Text library is maintained by the client or codix.')
                  ->change();
            $table->integer('njsch_mntd_by_clnt_id')
                  ->comment('If Night job scheduler is maintained by the client or codix.')
                  ->change();
            $table->integer('trans_mntd_by_clnt_id')
                  ->comment('If Translations are maintained by the client or codix.')
                  ->change();
        });

        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('se_mntd_by_clnt_id')
                  ->comment('If Expert System is maintained by the clinet or codix.')
                  ->change();
            $table->integer('tl_mntd_by_clnt_id')
                  ->comment('If Text library is maintained by the client or codix.')
                  ->change();
            $table->integer('njsch_mntd_by_clnt_id')
                  ->comment('If Night job scheduler is maintained by the client or codix.')
                  ->change();
            $table->integer('trans_mntd_by_clnt_id')
                  ->comment('If Translations are maintained by the client or codix.')
                  ->change();
        });

        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'se_mntd_by_clnt')
            ->update(['subtype' => 'se_mntd_by_codix']);

        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'tl_mntd_by_clnt')
            ->update(['subtype' => 'tl_mntd_by_codix']);

        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'njsch_mntd_by_clnt')
            ->update(['subtype' => 'njsch_mntd_by_codix']);  
            
        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'trans_mntd_by_clnt')
            ->update(['subtype' => 'trans_mntd_by_codix']);
            
        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'e_reggest_mntd_by_clnt')
            ->update(['subtype' => 'e_reggest_mntd_by_codix']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'se_mntd_by_codix')
            ->update(['subtype' => 'se_mntd_by_clnt']);

        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'tl_mntd_by_codix')
            ->update(['subtype' => 'tl_mntd_by_clnt']);
        
        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'njsch_mntd_by_codix')
            ->update(['subtype' => 'njsch_mntd_by_clnt']);
            
         DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'trans_mntd_by_codix')
            ->update(['subtype' => 'trans_mntd_by_clnt']);
            
        DB::table('enum_values')
            ->where('type', 'project_specific_feature')
            ->where('subtype', 'e_reggest_mntd_by_codix')
            ->update(['subtype' => 'e_reggest_mntd_by_clnt']);
    }
}
