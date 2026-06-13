<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('post_tag')) {
            return;
        }

        Schema::table('post_tag', function (Blueprint $table): void {
            if (!Schema::hasColumn('post_tag', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('post_tag', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('post_tag')) {
            return;
        }

        Schema::table('post_tag', function (Blueprint $table): void {
            if (Schema::hasColumn('post_tag', 'created_at')) {
                $table->dropColumn('created_at');
            }

            if (Schema::hasColumn('post_tag', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
