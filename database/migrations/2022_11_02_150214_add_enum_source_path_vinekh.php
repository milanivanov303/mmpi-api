<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnumSourcePathVinekh extends Migration
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
                        ->where('type','source_paths')
                        ->orderBy('sortindex', 'desc')
                        ->first('sortindex');

        //insert new source_path for vinekh
        DB::table('enum_values')->insert(
            [
                'type'           => 'source_paths', 
                'subtype'        => 'vinekh',
                'key'            => 'source_path_devops_extranet', 
                'value'          => 'http://vinekh.codixfr.private:8082/ui/native/extranet/', 
                'description'    => 'The extranet build could be accessed in http://vinekh.codixfr.private:8082/ui/native/extranet/extranet_war_name.war', 
                'active'         => 1, 
                'sortindex'      => $max_sortindex->sortindex + 1, 
                'changed_by'     => 728
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
        ->where('type', 'source_paths')
        ->where('key', 'source_path_devops_extranet')
        ->delete();
    }
}
