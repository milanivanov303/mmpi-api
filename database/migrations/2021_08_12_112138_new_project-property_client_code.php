<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewProjectPropertyClientCode extends Migration
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
                                'type'        => 'project_specific_feature', 
                                'subtype'     => 'numeric_clnt_code', 
                                'key'         => 'num_clnt_code', 
                                'value'       => 'The values are set in table project_specifics.', 
                                'description' => 'For each clnt_code (Cxxx ??? DONT_USE) from table projects have the corresponding numeric client code. Could be a number from 1 - 999.', 
                                'active'      => 1, 
                                'sortindex'   => $max_sortindex->sortindex + 1, 
                                'changed_by'  => 728 
                            ]

                          );
// +-----+--------------------------+-------------------+---------------+-----------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
// | id  | type                     | subtype           | key           | value                                         | description                                                                                                                               | url  | active | sortindex | extra_property | changed_on          | changed_by | imx_version |
//+-----+--------------------------+-------------------+---------------+-----------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
//| 598 | project_specific_feature | numeric_clnt_code | num_clnt_code | The values are set in table project_specifics | For each clnt_code (Cxxx ??? DONT_USE) from table projects have the corresponding numeric client code. Could be a number from 1 - 999.    | NULL |      1 |       419 | NULL           | 2021-03-04 16:10:14 |        728 | none        |
//+-----+--------------------------+-------------------+---------------+-----------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------------------+------+--------+-----------+----------------+---------------------+------------+-------------+
                          
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
            ->where('subtype', 'numeric_clnt_code')
            ->delete();
    }
}
