<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('topic', 255);
            $table->text('description')->nullable();
            $table->float('budget', 8,2)->nullable();
            $table->float('price', 8,2)->nullable();

            $table->text('bidding_instructions')->nullable();
            $table->enum('experience_required',[1,2,3]/* ['Beginer', 'Intermediate', 'Expert']*/);
            $table->tinyInteger('rating_required');

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('freelancer_id')->nullable();
            $table->dateTime('deadline');
            $table->enum('status', ['Unassigned', 'Incomplete', 'On-revision', 'Cancelled', 'Complete'])->default('Unassigned');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('orders');
    }
}
