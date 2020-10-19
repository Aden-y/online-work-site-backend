<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname',30);
            $table->string('middlename',30)->nullable();
            $table->string('lastname',30);
            $table->string('email',255)->unique();
            $table->string('username', 25)->unique();
            $table->string('password', 255);
            $table->unsignedBigInteger('phonenumber')->unique();
            $table->unsignedBigInteger('nationalid')->unique();
            $table->enum('type',['Admin', 'Freelancer', 'Client']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
