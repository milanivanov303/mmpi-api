<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumParamTableTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add more description which is shown in PR, patch
        DB::table('enum_values')
        ->where('type', 'translations_transfer_operation_type')
        ->where('key', 'param_table_translations')
        ->update(['description' => 'Parametrization Table Transfer. Modifies data in the selected table.
        - PRM_VALUES
        - PRM_LIST
        - PRM_PARAMS
        - PRM_LEVEL']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('enum_values')
        ->where('type', 'translations_transfer_operation_type')
        ->where('key', 'param_table_translations')
        ->update(['description' => 'Parametrization Table Transfer. Modifies data in the selected table.']);
    }
}
