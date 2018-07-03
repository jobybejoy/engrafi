<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id');
            $table->string('student_id');
            $table->string('session');
            $table->timestamps();
            // $table->foreign('event_id')->references('e_id')->on('events');
            // $table->foreign('student_id')->references('register_number')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_attendence');
    }
}
