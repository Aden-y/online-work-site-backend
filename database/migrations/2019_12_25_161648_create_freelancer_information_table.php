<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancerInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freelancer_information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('freelancer_id');
            $table->enum('experience_level',/*[
                'Beginer','Intermediate','Experts'
            ]*/[1,2,3]);
            $table->enum('education_level', ['None', 'Primary','Secondary','Certificate','Diploma','Digree','Masters','PhD']);
            $table->timestamps();
            $table->foreign('freelancer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freelancer_information');
    }
}
