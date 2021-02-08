<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumValuesTypeBusinessToProjectSpecificFeature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('enum_values')->where('type','type_business')->update([
                    "type" => "project_specific_feature",
                    "subtype" => "imx_activity"
                ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('enum_values')->where('type','project_specific_feature')->where('subtype','imx_activity')->update([
                    "type" => "type_business",
                    "subtype" => NULL
                ]);
    }
}
