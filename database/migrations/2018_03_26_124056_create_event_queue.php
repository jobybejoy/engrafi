<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id');
            $table->string('student_id')->nullable();
            $table->string('staff_id')->nullable();
            $table->string('name');
            $table->string('department');   
            $table->string('email');
            $table->timestamp('queued_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_queue');
    }
}
