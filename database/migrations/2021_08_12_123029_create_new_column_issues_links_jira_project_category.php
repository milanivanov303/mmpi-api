<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewColumnIssuesLinksJiraProjectCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add column jira_project_category in issues_links
        Schema::table('issues_links', function (Blueprint $table) {
            $table->char('jira_project_category', 50)
                  ->nullable()
                  ->comment('Project category taken from TTS.')
                  ->after('issue_link_type_id');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issues_links', function($table){
            $table->dropColumn('jira_project_category');
            }
        );
    }
}
