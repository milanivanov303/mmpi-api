<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableBusinessTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('business_teams');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('business_teams', function (Blueprint $table) {
            $table->integer('department_id');
                $table->dateTime('made_on')
                      ->default(DB::raw('CURRENT_TIMESTAMP'))
                      ->nullable();


            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onUpdate('cascade')
                  ->comment('List of Business Teams');

            $table->index('department_id');
        });

            //get department_ids from departments and inser business teams only
           $business_department_ids=array(); 
           $business_department_ids = DB::table('departments')                                                                                                                                                                                                                                             
                                          ->where('name', 'like', 'Business Team%')
                                          ->orWhere('name', 'like', 'BT%')
                                          ->where('status', 1)
                                          ->select('id')
                                          ->get();
                                          
            foreach ($business_department_ids as $business_department_id){
                DB::table('business_teams')->insert(
                        [ 
                         'department_id' => $business_department_id->id,  
                        ]
                );
            }                                       
        
    }
}
