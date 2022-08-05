<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertValuesTranslationTables extends Migration
{

    public function getEnum() {

        return DB::table('enum_values')
            ->where('type','translations_transfer_operation_type')
            ->where('key','param_table_translations')
            ->pluck('id')->toArray();
    }

    public function getImxTableIds() {
        return DB::table('imx_tables')
            ->where('table_name', 'like', 'PRM%')
            ->pluck('id')->toArray();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('enum_values')->insert(
            [
                [
                    'type'        => 'translations_transfer_operation_type',
                    'key'         => 'param_table_translations',
                    'value'       => 'Parametrization Table Transfer',
                    'description' => 'Parametrization Table Transfer. Modifies data in the selected table.',
                    'active'      => 1,
                    'sortindex'   => 4,
                    'changed_by'  => 2056,
                ]
            ]
        );

        $imxTableIds = $this->getImxTableIds();

        $enum = $this->getEnum();

        $max_sortindex = DB::table('translation_tables')
            ->orderBy('sortindex', 'desc')
            ->first('sortindex');

        foreach ($imxTableIds as $imxTableId) {
            $max_sortindex->sortindex++;
            DB::table('translation_tables')->insert(
                [
                    [
                        'imx_table_id'                       => $imxTableId,
                        'translations_transfer_operation_id' => $enum[0],
                        'added_by'                           => 2056,
                        'sortindex'                          => $max_sortindex->sortindex,
                        'comment'                            => 'Included in Parametrization Table Transfer oparation',
                    ]
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $enum = $this->getEnum();
        DB::table('translation_tables')->where('translations_transfer_operation_id', $enum[0])->delete();
    }
}
