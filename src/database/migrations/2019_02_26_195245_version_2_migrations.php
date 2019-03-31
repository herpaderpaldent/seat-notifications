<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
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
            $table->json('affiliations')->nullable()->default(null);
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
