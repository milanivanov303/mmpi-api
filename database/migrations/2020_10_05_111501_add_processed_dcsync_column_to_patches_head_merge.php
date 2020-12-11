<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessedDcsyncColumnToPatchesHeadMerge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patches_head_merge', function (Blueprint $table) {
            $table->integer('processed_dcsync')->default(0)->after('processed');
            $table->string('tts_keys_dcsync', 256)->nullable()->after('tts_keys');
        });
        
        Schema::table('patches_head_merge', function (Blueprint $table) {
            $table->renameColumn('processed', 'processed_headmerge');
            $table->renameColumn('tts_keys', 'tts_keys_headmerge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patches_head_merge', function (Blueprint $table) {
            $table->dropColumn('processed_dcsync');
            $table->dropColumn('tts_keys_dcsync');
        });
        
        Schema::table('patches_head_merge', function (Blueprint $table) {
            $table->renameColumn('processed_headmerge', 'processed');
            $table->renameColumn('tts_keys_headmerge', 'tts_keys');
        });
    }
}
