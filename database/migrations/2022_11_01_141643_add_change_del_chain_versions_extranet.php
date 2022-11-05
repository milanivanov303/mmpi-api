<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeDelChainVersionsExtranet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //map extranet version view to the DeVops one
        DB::table('enum_values')
        ->where('type', 'delivery_chain_version')
        ->where('subtype', 'EXTRANET')
        ->update(["value" => DB::raw("REPLACE(value,  'v', 'X')")]);

        DB::table('enum_values')
        ->where('type', 'delivery_chain_version')
        ->where('subtype', 'EXTRANET')
        ->update(["value" => DB::raw("REPLACE(value,  '.', '_')")]);

        //get max sortindex by type
        $max_sortindex = DB::table('enum_values')                                                                                                                                                                                                                                                  
        ->where('type','binary_types')
        ->orderBy('sortindex', 'desc')
        ->first('sortindex');

        //insert new extranet versions
        DB::table('enum_values')->insert(
            [
                [
                    'type'           => 'delivery_chain_version', 
                    'subtype'        => 'EXTRANET',
                    'key'            => 'X4', 
                    'value'          => 'X4', 
                    'description'    => 'Exrtranet X4', 
                    'active'         => 1, 
                    'sortindex'      => $max_sortindex->sortindex + 1, 
                    'changed_by'     => 728
                ],
                [
                    'type'           => 'delivery_chain_version', 
                    'subtype'        => 'EXTRANET',
                    'key'            => 'X5', 
                    'value'          => 'X5', 
                    'description'    => 'Exrtranet X5', 
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
        DB::table('enum_values')
        ->where('type', 'delivery_chain_version')
        ->where('subtype', 'EXTRANET')
        ->update(["value" => DB::raw("REPLACE(value,  'X', 'v')")]);

        DB::table('enum_values')
        ->where('type', 'delivery_chain_version')
        ->where('subtype', 'EXTRANET')
        ->update(["value" => DB::raw("REPLACE(value,  '_', '.')")]);

        DB::table('enum_values')
        ->where('type', 'delivery_chain_version')
        ->where('subtype','EXTRANET')
        ->whereIn('key', ['X4', 'X5'])
        ->delete();
    }
}
