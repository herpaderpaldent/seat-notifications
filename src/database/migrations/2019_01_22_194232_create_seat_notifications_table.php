<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('seatnotifications');

        Schema::table('herpaderp_discord_users', function (Blueprint $table) {
            $table->string('channel_id')->change();
            $table->unique('channel_id', 'herpaderp_discord_channel_id_unique');
        });

        Schema::create('herpaderp_seat_notification_recipients', function (Blueprint $table) {
            $table->string('channel_id');
            $table->string('notification_channel');
            $table->timestamps();

            $table->primary('channel_id', 'herpaderp_seat_notification_recipients_primary');
        });

        Schema::create('herpaderp_seat_notification_notification_recipients', function (Blueprint $table) {
            $table->string('channel_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('channel_id',  'seat_notification_notification_recipients_foreign')
                ->references('channel_id')
                ->on('herpaderp_seat_notification_recipients')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('herpaderp_seat_notification_notification_recipients');
        Schema::dropIfExists('herpaderp_seat_notification_recipients');

        Schema::create('seatnotifications', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('settings_id');
            $table->string('settings_type');
            $table->timestamps();
        });
    }
}
