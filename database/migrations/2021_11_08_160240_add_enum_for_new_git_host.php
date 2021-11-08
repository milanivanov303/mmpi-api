<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnumForNewGitHost extends Migration
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
                ->where('type','repository_type')
                ->orderBy('sortindex', 'desc')
                ->first('sortindex');

        DB::table('enum_values')->insert(
            [
                [
                    'type'           => 'repository_type', 
                    'subtype'        => 'repo_gitAlldc',
                    'extra_property' => '',
                    'url'            => 'https://idw.codixfr.private',
                    'key'            => 'idw', 
                    'value'          => 'IDEALWINE', 
                    'description'    => 'IDEALWINE repository', 
                    'active'         => 1, 
                    'sortindex'      => $max_sortindex->sortindex + 1, 
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
                ->where('type', 'repository_type')
                ->where('value', 'IDEALWINE')
                ->delete();
    }
}
