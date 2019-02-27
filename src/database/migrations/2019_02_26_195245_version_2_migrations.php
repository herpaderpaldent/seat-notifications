<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Version2Migrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('herpaderp_seat_notification_notification_recipients');
        Schema::dropIfExists('herpaderp_seat_notification_recipients');

        Schema::create('herpaderp_seat_notification_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('driver_id');
            $table->string('driver');
            $table->integer('group_id')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['driver_id', 'driver'], 'herpaderp_seat_notification_recipients_unique');
        });

        Schema::create('herpaderp_seat_notification_subscriptions', function (Blueprint $table) {
            $table->integer('recipient_id')->unsigned();
            $table->string('notification');
            $table->json('affiliation')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('recipient_id', 'herpaderp_seat_notification_subscriptions_foreign')
                ->references('id')
                ->on('herpaderp_seat_notification_recipients')
                ->onDelete('cascade');

            $table->unique(['recipient_id', 'notification'], 'herpaderp_seat_notification_subscriptions_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('herpaderp_seat_notification_subscriptions');
        Schema::dropIfExists('herpaderp_seat_notification_recipients');

        Schema::create('herpaderp_seat_notification_recipients', function (Blueprint $table) {
            $table->string('channel_id');
            $table->string('notification_channel');
            $table->boolean('is_channel')->default(false);
            $table->timestamps();

            $table->primary('channel_id', 'herpaderp_seat_notification_recipients_primary');
        });

        Schema::create('herpaderp_seat_notification_notification_recipients', function (Blueprint $table) {
            $table->string('channel_id');
            $table->string('name');
            $table->json('affiliation')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('channel_id', 'seat_notification_notification_recipients_foreign')
                ->references('channel_id')
                ->on('herpaderp_seat_notification_recipients')
                ->onDelete('cascade');
        });
    }
}
