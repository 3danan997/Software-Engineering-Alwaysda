<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('contact_id')->index()->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->text("reminder_type");
            $table->text("reminder_frequency");
            $table->text("reminder_frequency_value")->nullable();
            $table->text("reminder_time");
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
        Schema::dropIfExists('reminders');
    }
}
