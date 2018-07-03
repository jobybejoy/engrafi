<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('e_id');
            $table->string('staff_id');
            $table->string('staff_name');
            $table->string('name');
            $table->text('description');
            $table->date('date');
            $table->time('time');
            $table->integer('sessions')->default(1);
            $table->string('card_image')->default('/event/default.jpg');
            $table->string('venue')->nullable();
            $table->integer('max_participant');
            $table->integer('registered')->default(0);
            $table->string('resource_person');
            $table->string('department');
            $table->string('category');
            $table->timestamps();
            // $table->foreign('staff_id')->references('staff_id')->on('faculties');
            // $table->foreign('staff_id')->references('_id')->on('faculties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
