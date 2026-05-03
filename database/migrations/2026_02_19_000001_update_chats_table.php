<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->string('uid')->unique()->after('id');
            $table->foreignId('ad_id')->constrained('ads')->onDelete('cascade')->after('uid');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade')->after('ad_id');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade')->after('sender_id');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['ad_id']);
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropColumn(['uid', 'ad_id', 'sender_id', 'receiver_id']);
        });
    }
};
