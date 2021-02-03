<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEnumValuesChangeUniqueTypeKey extends Migration
{
    /**
     * Run the migrations.
     * Change unique key from type_key to type_subtype_key
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('enum_values', function (Blueprint $table) {
            $table->dropUnique('idx_enum_values_unq_type_key');
        });
        Schema::table('enum_values', function (Blueprint $table) {
            $table->unique([
                'type',
                'subtype',
                'key'
            ], 'idx_enum_values_unq_type_subtype_key');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('enum_values', function (Blueprint $table) {
            $table->dropUnique('idx_enum_values_unq_type_subtype_key');
        });
        Schema::table('enum_values', function (Blueprint $table) {
            $table->unique([
                'type',
                'key'
            ], 'idx_enum_values_unq_type_key');
        });
        Schema::enableForeignKeyConstraints();
    }
}
