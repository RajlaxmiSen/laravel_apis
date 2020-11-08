<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('from_user');
            $table->foreign('from_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('to_user');
            $table->foreign('to_user')->references('id')->on('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->tinyInteger('is_read')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('messages');
    }
}
