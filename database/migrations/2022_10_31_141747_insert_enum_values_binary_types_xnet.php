<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnumValuesBinaryTypesXnet extends Migration
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
                ->where('type','binary_types')
                ->orderBy('sortindex', 'desc')
                ->first('sortindex');

        //insert new binary type for xnet
        DB::table('enum_values')->insert(
            [
                [
                    'type'           => 'binary_types', 
                    'subtype'        => 'xnet-admin',
                    'key'            => 'xnet-admin-file', 
                    'value'          => 'X4 Extranet Admin', 
                    'description'    => 'Extranet admin is deployed on the Web Logic on the iMX node.', 
                    'active'         => 1, 
                    'sortindex'      => $max_sortindex->sortindex + 1, 
                    'changed_by'     => 728
                ],
                [
                    'type'           => 'binary_types', 
                    'subtype'        => 'xnet',
                    'key'            => 'xnet-file', 
                    'value'          => 'X4 Extranet Public', 
                    'description'    => 'Public Extranet is deployed as before, on the separate extranet node.', 
                    'active'         => 1, 
                    'sortindex'      => $max_sortindex->sortindex + 2, 
                    'changed_by'     => 728
                ]           
            ]
                );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //the values could be deleted only if there are no records in modifications table with thses enums
        DB::table('enum_values')
                ->where('type', 'binary_types')
                ->where('subtype','like', 'xnet%')
                ->delete();
    }
}
