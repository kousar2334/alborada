<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pages already carry a `build_with` flag (editor vs builder), but nothing ever
 * set it and the frontend ignored it, so admins could only write body copy into
 * the WYSIWYG — there was no way to design a page freely. Design mode renders
 * the page full-width and lets the admin ship their own section markup, which
 * needs somewhere to keep the page's own styles.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'custom_css')) {
                $table->text('custom_css')->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('custom_css');
        });
    }
};
