<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('feed_text')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->string('value_added')->default(0);         
            $table->tinyInteger('status')->default(0);
            $table->text('admin_comment')->nullable();
            $table->tinyInteger('is_share')->default(0);
            $table->string('shared_feed_id')->nullable();
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
        Schema::dropIfExists('feeds');
    }
}
