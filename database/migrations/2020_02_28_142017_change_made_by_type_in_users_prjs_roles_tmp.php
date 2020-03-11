<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMadeByTypeInUsersPrjsRolesTmp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('users_prjs_roles_tmp', function (Blueprint $table) {
            $table->dropColumn('made_by');
        });

        Schema::table('users_prjs_roles_tmp', function (Blueprint $table) {
            $table->integer('made_by')->nullable(false);
            $table->foreign('made_by')
                  ->references('id')
                  ->on('users')
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
        Schema::table('users_prjs_roles_tmp', function (Blueprint $table) {
            $table->dropColumn('made_by');
        });

        Schema::table('users_prjs_roles_tmp', function (Blueprint $table) {
            $table->string('made_by');
        });
        Schema::enableForeignKeyConstraints();    
    }
}
