<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefreshTokenNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('herpaderp_refresh_token_notifications', function (Blueprint $table) {
            $table->string('channel_id');
            $table->enum('type', ['private', 'channel']);
            $table->string('via');
            $table->timestamps();

            $table->primary('channel_id', 'herpaderp_refresh_token_notification_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('herpaderp_refresh_token_notifications');
    }
}
