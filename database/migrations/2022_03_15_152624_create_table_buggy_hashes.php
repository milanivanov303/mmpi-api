<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBuggyHashes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hash_buggies', function (Blueprint $table) {
            $table->charset ='utf8';
            $table->collation = 'utf8_general_ci';
            $table->string('buggy_hash',100)->nullable(false)->comment('Hash of the commit which is marked as buggy');
            $table->string('repo_path',256)->default(null)->comment('Hash repository.');
            $table->string('linked_branch',256)->default(null)->comment('Linked branch to delivery chain.');
            $table->string('fix_hash',100)->default(null)->comment('Hash of the commit where the buggy one is fixed');
            $table->integer('marked_buggy_by')->nullable(false)->comment('Id of the owner of the commit');
            $table->timestamp('marked_buggy_on')->useCurrent()->comment('Timestamp of the record');

            $table->unique(["buggy_hash", "linked_branch"], 'idx_buggy_hash_unq');

            $table->foreign('marked_buggy_by')
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
        Schema::dropIfExists('hash_buggies');
        Schema::enableForeignKeyConstraints();
    }
}
