<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModifyStructureWarningEnums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add enums for searching "g_personnel" pattern in name/content columns from table modifiations
        DB::table('enum_values')->insert(
            [
                'type'        => 'modify_structure_warning', 
                'key'         => 'modify_structure_g_personnel_warning', 
                'value'       => 'g_personnel', 
                'description' => 'Use as pattern to grep for "g_personnel" in modifications name/content.', 
                'active'      => 1, 
                'sortindex'   => 1, 
                'changed_by'   => 728
            ] 
        );

    //mysql>  select * from enum_values where type='modify_structure_warning';
    //+-----+--------------------------+---------+--------------------------------------+-------------+----------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
    //| id  | type                     | subtype | key                                  | value       | description                                              | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
    //+-----+--------------------------+---------+--------------------------------------+-------------+----------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
    //| 625 | modify_structure_warning | NULL    | modify_structure_g_personnel_warning | g_personnel | Use as pattern to search "g_personnel" in modifications. | NULL |      1 |         1 | NULL           | 2021-10-06 16:05:28 |        728 | none        |
    //+-----+--------------------------+---------+--------------------------------------+-------------+----------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+

        //add coulumn table_transfer_warning_sent in modifications
        Schema::table('modifications', function (Blueprint $table) {
                   $table->boolean('modify_structure_warning_sent')
                         ->nullable()
                         ->comment('When modification from type table/Modify structure contains modify_structure_warning is added/updated send mail, 1 - sent, 0/NULL - not sent.')
                         ->after('bad_content_confirmed');
                
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
            $table->dropColumn('modify_structure_warning_sent');
        });

        DB::table('enum_values')->where('key', 'modify_structure_g_personnel_warning')->delete();
    }
}
