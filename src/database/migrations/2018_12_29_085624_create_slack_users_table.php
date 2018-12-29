<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlackUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('herpaderp_slack_users', function (Blueprint $table) {
            $table->unsignedInteger('group_id');
            $table->string('slack_id');
            $table->string('channel_id');
            $table->timestamps();

            $table->primary('group_id', 'herpaderp_slack_users_primary');
            $table->unique('slack_id', 'herpaderp_slack_users_slack_id_unique');
            $table->unique('channel_id', 'herpaderp_slack_users_channel_id_unique');

            $table->foreign('group_id', 'herpaderp_slack_users_group_foreign')
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
        Schema::dropIfExists('herpaderp_slack_users');
    }
}
