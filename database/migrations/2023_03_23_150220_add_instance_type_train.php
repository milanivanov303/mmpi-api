<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstanceTypeTrain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('instance_types')->insert(
            [
                'id'        => 'TRAIN',
                'title'         => 'Training'
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
        DB::table('instance_types')
            ->where('id', 'TRAIN')
            ->delete();
    }
}
