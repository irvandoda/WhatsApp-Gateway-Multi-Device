<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterChatMessagesAddReplyAndWappId extends Migration
{
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('wapp_id', 64)->nullable()->after('id');
            $table->unsignedBigInteger('reply_message_id')->nullable()->after('wapp_id');
            $table->index('wapp_id');
            $table->index('reply_message_id');
            $table->foreign('reply_message_id')->references('id')->on('chat_messages')->onDelete('set null');
            $table->unique(['session_id','wapp_id'], 'chat_messages_session_wapp_unique');
        });
    }

    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropUnique('chat_messages_session_wapp_unique');
            $table->dropForeign(['reply_message_id']);
            $table->dropIndex(['wapp_id']);
            $table->dropIndex(['reply_message_id']);
            $table->dropColumn(['wapp_id','reply_message_id']);
        });
    }
}
