<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FillProjectsTypeBusinessToProjectSpecifics extends Migration
{
    /**
     * Run the migrations.
     * Fill records from projects.type_business into project_specifics table. Drop column projects.type_business 
     * @return void
     */
    public function up()
    {
        $projects = DB::table('projects')->select('id','name','type_business')->get();       
        $enums = DB::table('enum_values')->where('type','project_specific_feature')->where('subtype','imx_activity')->select('id','description')->get()->toArray();
        
        $comments = array_combine(array_map(function($b) {
                                                return $b->id;
                                            }, $enums), array_map(function($b) {
                                                return $b->description;
                                            }, $enums)
                                    );

        foreach ($projects as $key => $project) {
            if($project->type_business !== NULL) {
                $businesses = explode(",", $project->type_business);
                
                foreach ($businesses as $businessIndex => $business) {
                    
                    DB::table('project_specifics')->insert([
                        "project_id" => $project->id,
                        "prj_specific_feature_id" => $business,
                        "made_by" => 1638,
                        "comment" => $comments[$business]
                    ]);
                }
            }
        }
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('type_business');
        });
    }

    /**
     * Reverse the migrations.
     * Create column projects.type_business + fill records from project_specifics table into projects.type_business + delete records from project_specifics.
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type_business', 80)->nullable()->after('modified_by_id');
        });

        $projectSpecificFeatures = DB::select(
                "SELECT * FROM project_specifics WHERE prj_specific_feature_id IN 
                (SELECT id FROM enum_values WHERE `type`='project_specific_feature' AND subtype='imx_activity');");

        $featuresByProjects = [];
        foreach($projectSpecificFeatures as $key => $projectSpecificFeature) {
            if(!isset($featuresByProjects[$projectSpecificFeature->project_id])) {
                $featuresByProjects[$projectSpecificFeature->project_id] = [];
            }
            array_push($featuresByProjects[$projectSpecificFeature->project_id], $projectSpecificFeature);
        }

        foreach ($featuresByProjects as $projectId => $featuresByProject) {
            $typeBusinessString = count($featuresByProject) > 1 ? implode("," , array_map( function($a) {
                            return $a->prj_specific_feature_id;
                } , $featuresByProject)) : $featuresByProject[0]->prj_specific_feature_id;
            
            DB::table('projects')->where('id',$projectId)->update([
                        "type_business" => $typeBusinessString
                    ]);
        }
        
        DB::delete(
                "DELETE FROM project_specifics WHERE prj_specific_feature_id IN 
                    (SELECT id FROM enum_values WHERE `type`='project_specific_feature' AND subtype='imx_activity');");
    }

}
