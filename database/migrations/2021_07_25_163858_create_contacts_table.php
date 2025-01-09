<?php

/**
 *
 * @author: Mohammad Hammado 14.07.2022, 08:10
 * @version 1.0
 * @since 1.0
 * @description:    Migration of table "roles"
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->text("avatar")->nullable();
            $table->text("first_name");
            $table->text("last_name");
            $table->text("mobile_number")->nullable();
            $table->date("birthday");
            $table->text("priority");
            $table->text("social_media")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('contacts');
    }
}
