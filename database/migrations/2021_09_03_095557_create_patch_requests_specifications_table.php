<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatchRequestsSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patch_requests_specifications', function (Blueprint $table) {
            $table->charset ='utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('patch_request_id')->default(null);
            $table->integer('user_id')->default(null);
            $table->string('specification',80)->default(null);
            $table->integer('made_by')->default(null);
            $table->timestamp('made_on')->useCurrent();
            $table->foreign('patch_request_id')
                ->references('id')
                ->on('patch_requests')
                ->onUpdate('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
            $table->foreign('made_by')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('patch_requests_specifications');
        Schema::enableForeignKeyConstraints();
    }
}
