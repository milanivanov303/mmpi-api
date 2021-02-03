<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FillProjectsTypeBusinessToProjectSpecifics extends Migration
{
    /**
     * Run the migrations.
     * Fill records from projects.type_business into project_specifics table. Drop column projects.type_business 
     * @return void
     */
    public function up()
    {
        $projects = DB::table('projects')->whereNotNull('type_business')->select('id','name','type_business')->get();
        $enums = DB::table('enum_values')->where('type','project_specific_feature')->where('subtype','imx_activity')->select('id','description')->get();

        foreach ($projects as $key => $project) {
                $businesses = explode(",", $project->type_business);
                
                foreach ($businesses as $businessIndex => $business) {
                    
                    DB::table('project_specifics')->insert([
                        "project_id" => $project->id,
                        "prj_specific_feature_id" => $business,
                        "made_by" => User::where('username','mmpi_auto')->first()->id,
                        "comment" => $enums->firstWhere('id', $business)->description
                    ]);
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

        $projectSpecificFeatures = DB::table('project_specifics')
                                        ->whereIn('prj_specific_feature_id', function($query)
                                        {
                                            $query->select('id')
                                                  ->from('enum_values')
                                                  ->where('type','project_specific_feature')
                                                  ->where('subtype','imx_activity');
                                        })->get();

        $featuresByProjects = $projectSpecificFeatures->groupBy('project_id');

        foreach ($featuresByProjects as $projectId => $featuresByProject) {

            $typeBusinessString = $featuresByProject->count() > 1 ? implode("," , array_map( function($a) {
                            return $a->prj_specific_feature_id;
                } , $featuresByProject->toArray())) : $featuresByProject[0]->prj_specific_feature_id;
            
            DB::table('projects')->where('id',$projectId)->update([
                        "type_business" => $typeBusinessString
                    ]);
        }

        DB::delete(
                "DELETE FROM project_specifics WHERE prj_specific_feature_id IN 
                    (SELECT id FROM enum_values WHERE `type`='project_specific_feature' AND subtype='imx_activity');");
    }

}
