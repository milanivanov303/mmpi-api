<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectEventNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_event_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('project_event_id');
            $table->integer('department_id');
            $table->integer('made_by');
            $table->datetime('made_on');
            $table->foreign('made_by')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade');
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onUpdate('cascade');
            $table->foreign('project_event_id')
                  ->references('id')
                  ->on('project_events')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('project_event_notifications');
        Schema::enableForeignKeyConstraints();
    }
}
