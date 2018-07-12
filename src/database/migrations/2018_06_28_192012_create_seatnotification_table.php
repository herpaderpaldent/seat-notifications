<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatnotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seatnotifications', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->bigInteger('character_id')->references('character_id')->on('character_infos')->onDelete('cascade')->nullable();
            $table->bigInteger('corporation_id')->references('corporation_id')->on('corporation_infos')->onDelete('cascade')->nullable();
            $table->enum('method',['email','slack','discord']);
            $table->enum('notification',['RefreshTokenDeleted']);
            $table->string('webhook')->nullable();
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
        Schema::dropIfExists('seatnotifications');
    }
}
