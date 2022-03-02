<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFlagCreateTaskScreenV9Modifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add coulumn flag_create_task_screen_V9 in modifications
        Schema::table('modifications', function (Blueprint $table) {
                 $table->boolean('flag_create_task_screen_V9')
                      ->nullable()
                      ->comment('When TTS task for screens V9 has been created, 1 - created, 0/NULL - not created.')
                      ->after('modify_structure_warning_sent');              
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column and enum
        Schema::table('modifications', function($table){
                 $table->dropColumn('flag_create_task_screen_V9');
        });
    }
}
