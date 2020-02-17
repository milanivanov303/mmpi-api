<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPrjsRolesTmp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('users_prjs_roles_tmp', function (Blueprint $table) {
            $table->charset ='utf8';
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->integer('user_id')->nullable(false);
            $table->integer('project_id')->nullable(false);
            $table->string('role_id', 16)->nullable(false);
            $table->string('made_by');
            $table->datetime('made_on');
            $table->longText('comment');
            $table->enum('status', ['0', '1'])->default(0);
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade');
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onUpdate('cascade');
            $table->foreign('role_id')
                  ->references('id')
                  ->on('user_roles')
                  ->onUpdate('cascade');
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
        Schema::dropIfExists('users_prjs_roles_tmp');
        Schema::enableForeignKeyConstraints();
    }
}
