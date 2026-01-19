<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('body', 100);
            $table->string('phone_number', 20);
            $table->string('push_name')->default('');
			$table->string('cs_name')->default('');
			$table->string('profile_sender')->nullable();
			$table->string('profile_receive')->nullable();
			$table->integer('stop_ai')->default(0);
            $table->text('last_message')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id','body','phone_number'], 'chat_sessions_user_body_phone_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_sessions');
    }
}
