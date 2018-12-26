<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscordUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('herpaderp_discord_users', function (Blueprint $table) {
            $table->unsignedInteger('group_id');
            $table->bigInteger('discord_id');
            $table->bigInteger('channel_id');
            $table->timestamps();

            $table->primary('group_id', 'herpaderp_discord_users_primary');
            $table->unique('discord_id', 'herpaderp_discord_users_discord_id_unique');

            $table->foreign('group_id', 'herpaderp_discord_users_group_foreign')
                ->references('id')
                ->on('groups')
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
        Schema::dropIfExists('herpaderp_discord_users');
    }
}
