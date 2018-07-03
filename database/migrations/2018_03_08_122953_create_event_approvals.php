<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventApprovals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_approvals', function (Blueprint $table) {
            $table->increments('approval_id');
            $table->string('staff_id');
            $table->string('staff_name');
            $table->string('name');
            $table->text('description');
            $table->date('date');
            $table->time('time');
            $table->integer('sessions')->default(1);
            $table->string('venue')->nullable();
            $table->integer('max_participant');
            $table->string('card_image')->default('event/default.jpeg');
            $table->string('resource_person');
            $table->string('department');
            $table->string('category');
            $table->string('approval_status');
            $table->string('approval_personal');
            $table->boolean('denied')->default(false);
            $table->string('message')->nullable();
            $table->timestamps();
            // $table->foreign('staff_id')->references('staff_id')->on('faculties');
            // $table->foreign('approval_personal')->references('staff_id')->on('faculties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_approvals');
    }
}
