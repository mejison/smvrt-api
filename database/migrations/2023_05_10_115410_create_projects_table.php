<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->date('due_date');
            $table->text('summary')->nullable();
            $table->enum('status', [
                                    'overdue', 
                                    'in-progress', 
                                    'internal-approval', 
                                    'new-version-sent',
                                    'new-version-recived',
                                    'to-sign'
                                ]);
            $table->integer('document_id');
            $table->integer('reminder_id');
            $table->integer('team_id');
            $table->timestamps();

            $table->index('document_id')->foreign('document_id')->references('id')->on('documents');
            $table->index('reminder_id')->foreign('reminder_id')->references('id')->on('reminders');
            $table->index('team_id')->foreign('team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
