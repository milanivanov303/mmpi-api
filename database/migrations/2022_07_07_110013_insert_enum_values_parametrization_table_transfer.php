<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnumValuesParametrizationTableTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('imx_tables')->insert(
            [
                [
                    'table_name'   => 'PRM_VALUES',
                    'added_by'     => 2056,
                    'comment'      => 'Included in Parametrization Table Transfer oparation',
                ],
                [
                    'table_name'   => 'PRM_LIST',
                    'added_by'     => 2056,
                    'comment'      => 'Included in Parametrization Table Transfer oparation',
                ],
                [
                    'table_name'   => 'PRM_PARAMS',
                    'added_by'     => 2056,
                    'comment'      => 'Included in Parametrization Table Transfer oparation',
                ],
                [
                    'table_name'   => 'PRM_LEVEL',
                    'added_by'     => 2056,
                    'comment'      => 'Included in Parametrization Table Transfer oparation',
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
        DB::table('imx_tables')->where('table_name','like', 'PRM%')->delete();
    }
}
