<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->text("reminders");
            $table->unsignedBigInteger('user_id')->index()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
