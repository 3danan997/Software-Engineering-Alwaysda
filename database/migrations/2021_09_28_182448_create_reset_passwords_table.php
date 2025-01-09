<?php

/**
 *
 * @author: Mohammad Hammado 14.07.2022, 08:20
 * @version 1.0
 * @since 1.0
 * @description:    Migration of table "logs_passwords" [morphable::auditable]
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResetPasswordsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('passwords_resets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text("token")->comment("Random generated ID");
            $table->timestamp("used_at")->nullable();
            $table->timestamp("created_at")->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('passwords_resets');
    }
}
