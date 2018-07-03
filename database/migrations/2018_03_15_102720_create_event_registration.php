<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_registration', function (Blueprint $table) {
            $table->increments('registration_id');
            $table->integer('event_id');
            $table->string('student_id')->nullable();
            $table->string('staff_id')->nullable();
            $table->string('name');
            $table->string('department');   
            $table->string('email');
            // Foreign keys were not migrated ----- having issue in migration -----------
            // $table->foreign('event_id')->references('e_id')->on('events');
            // $table->foreign('student_id')->references('register_number')->on('students');
            // $table->foreign('staff_id')->references('staff_id')->on('faculties');
            // $table->foreign('email')->references('email')->on('users');
            $table->timestamp('registered_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_registration');
    }
}
