<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->string('number', 100);
            $table->enum('direction', ['incoming','outgoing']);
            $table->text('message');
            $table->string('type')->default('text');
            $table->string('push_name')->default('');
            $table->text('attachment');
            $table->string('original_file')->default('');
            $table->timestamps();
            $table->foreign('session_id')->references('id')->on('chat_sessions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
