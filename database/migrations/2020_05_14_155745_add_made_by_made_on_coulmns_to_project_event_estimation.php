<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMadeByMadeOnCoulmnsToProjectEventEstimation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('project_event_estimations', function (Blueprint $table) {
            $table->integer('made_by');
            $table->datetime('made_on');
            $table->foreign('made_by')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade');
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
        Schema::table('project_event_estimations', function (Blueprint $table) {
            $table->dropColumn('made_by');
            $table->dropColumn('made_on');
        });
        Schema::enableForeignKeyConstraints();
    }
}
