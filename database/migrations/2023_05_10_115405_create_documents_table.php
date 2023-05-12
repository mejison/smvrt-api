<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            
            $table->integer('type_id')->unsigned();
            // $table->foreign('type_id')->references('id')->on('document_types');

            $table->integer('user_id');
            // $table->foreign('user_id')->references('id')->on('users');

            $table->integer('category_id');
            // $table->foreign('category_id')->references('id')->on('categories');

            $table->string('path');
            $table->boolean('need_to_approve')->default(false);

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
        Schema::dropIfExists('documents');
    }
}
