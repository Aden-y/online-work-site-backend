<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancerDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freelancer_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('freelancer_id');
            $table->string('academic_certificate', 255)->nullable();
            $table->string('resume', 255)->nullable();
            $table->timestamps();

            $table->foreign('freelancer_id')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freelancer_documents');
    }
}
