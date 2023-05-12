<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TeamMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->integer('team_id')->nullabel();
            $table->integer('user_id')->nullabel();
            $table->integer('role_id')->nullabel();
            $table->timestamps();

            $table->index('team_id')->foreign('team_id')->references('id')->on('teams');
            $table->index('user_id')->foreign('user_id')->references('id')->on('users');
            $table->index('role_id')->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
