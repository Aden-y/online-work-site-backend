<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancerSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freelancer_skills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('freelancer_id');
            $table->unsignedBigInteger('skill_id');

            $table->foreign('freelancer_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

            $table->foreign('skill_id')
            ->references('id')
            ->on('skills')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freelancer_skills');
    }
}
