<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumValuesTypeBusinessSortindex extends Migration
{
    /**
     * Update sortindex of enum_values type=type_business in order to be updated to type=project_specific_feature subtype=imx_activity.
     *
     * @return void
     */
    public function up()
    {
        $sortindexCollection = DB::table('enum_values')->select('id','sortindex')->orderBy('sortindex','desc')->limit(1)->get();
        $sortindex = $sortindexCollection[0]->sortindex;

        $enums = DB::table('enum_values')->where('type','type_business')->select('id','type','key','sortindex')->orderBy('sortindex','asc')->get();

        foreach ($enums as $enum) {
            $sortindex++;
            DB::table('enum_values')->where('id',$enum->id)->where('type','type_business')->update([
                        "sortindex" => $sortindex
                    ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //???
    }
}
