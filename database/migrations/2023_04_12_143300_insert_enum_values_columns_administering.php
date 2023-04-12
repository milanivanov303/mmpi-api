<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnumValuesColumnsAdministering extends Migration
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
        Schema::disableForeignKeyConstraints();
        DB::table('enum_values')->insert(
            [
                [
                    'type'        => 'project_specific_feature',
                    'subtype'     => 'db_ti_administering',
                    'key'         => 'admin_codix',
                    'value'       => 'CODIX',
                    'description' => 'DB and TI administering.',
                    'active'      => 1,
                    'sortindex'   => $max_sortindex->sortindex + 1,
                    'changed_by'  => 2056
                ],
                [
                'type'        => 'project_specific_feature',
                'subtype'     => 'db_ti_administering',
                'key'         => 'admin_client',
                'value'       => 'CLIENT',
                'description' => 'DB and TI administering.',
                'active'      => 1,
                'sortindex'   => $max_sortindex->sortindex + 2,
                'changed_by'  => 2056
                ],
                [
                    'type'        => 'project_specific_feature',
                    'subtype'     => 'db_ti_administering',
                    'key'         => 'admin_undefined',
                    'value'       => 'UNDEFINED',
                    'description' => 'DB and TI administering.',
                    'active'      => 1,
                    'sortindex'   => $max_sortindex->sortindex + 3,
                    'changed_by'  => 2056
                ]
            ]
        );


        //add columns in projects
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('db_administering')
                ->default(745)
                ->comment('DB administering for project.')
                ->after('project_stage');
            $table->integer('ti_administering')
                ->default(745)
                ->comment('TI administering for project.')
                ->after('db_administering');
            $table->foreign('db_administering','fk_db_administering')
                ->references('id')
                ->on('enum_values')
                ->onUpdate('cascade');
            $table->foreign('ti_administering', 'fk_ti_administering')
                ->references('id')
                ->on('enum_values')
                ->onUpdate('cascade');
        });
        //add columns in project_history
        Schema::table('projects_history', function (Blueprint $table) {
            $table->integer('db_administering')
                ->default(745)
                ->comment('DB administering for project.')
                ->after('project_stage');

            $table->integer('ti_administering')
                ->default(745)
                ->comment('TI administering for project.')
                ->after('db_administering');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('projects', function($table){
            $table->dropForeign('db_administering');
            $table->dropForeign('ti_administering');
            $table->dropColumn('db_administering');
            $table->dropColumn('ti_administering');
        });

        Schema::table('projects_history', function($table){
            $table->dropColumn('db_administering');
            $table->dropColumn('ti_administering');
        });

        DB::table('enum_values')->where('subtype', 'db_ti_administering')->delete();
        Schema::enableForeignKeyConstraints();
    }
}
