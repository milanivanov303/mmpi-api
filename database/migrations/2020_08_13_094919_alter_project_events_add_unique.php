<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProjectEventsAddUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('project_events', function (Blueprint $table) {
            $table->unique([
                'project_id',
                'project_event_type_id',
                'project_event_subtype_id',
                'project_event_status'
            ], 'unique_project_events');
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
        Schema::table('project_events', function (Blueprint $table) {
            $table->dropUnique('unique_project_events');
        });
        Schema::enableForeignKeyConstraints();
    }
}
