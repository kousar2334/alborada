<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade')->after('id');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade')->after('chat_id');
            $table->text('message')->after('sender_id');
            $table->boolean('is_read')->default(false)->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['chat_id', 'sender_id', 'message', 'is_read']);
        });
    }
};
