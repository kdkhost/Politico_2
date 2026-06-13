<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, array{created?: string, updated?: string}> */
    private array $tables = [
        'permission_groups' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'permissions' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'password_reset_tokens' => ['created' => 'new_created_at'],
        'modules' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'settings' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'smtp_settings' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'license_settings' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'categories' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'tags' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'posts' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'pages' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'media' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'media_usage' => ['created' => 'new_created_at'],
        'events' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'financial_categories' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'financial_transactions' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'transparency_items' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'visits' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'logs' => ['created' => 'new_created_at'],
        'notifications' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'contacts' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'newsletter_subscribers' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'backups' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'waf_logs' => ['created' => 'new_created_at'],
        'menus' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'menu_items' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'hashtags' => ['created' => 'new_created_at', 'updated' => 'new_updated_at'],
        'hashtag_ables' => ['created' => 'new_created_at'],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $legacyColumns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $needsCreatedAt = !Schema::hasColumn($table, 'created_at');
            $needsUpdatedAt = !Schema::hasColumn($table, 'updated_at');

            if ($needsCreatedAt || $needsUpdatedAt) {
                Schema::table($table, function (Blueprint $blueprint) use ($needsCreatedAt, $needsUpdatedAt): void {
                    if ($needsCreatedAt) {
                        $blueprint->timestamp('created_at')->nullable();
                    }

                    if ($needsUpdatedAt) {
                        $blueprint->timestamp('updated_at')->nullable();
                    }
                });
            }

            $this->copyLegacyTimestamp($table, 'created_at', $legacyColumns['created'] ?? null);
            $this->copyLegacyTimestamp($table, 'updated_at', $legacyColumns['updated'] ?? null);
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                if (Schema::hasColumn($table, 'created_at')) {
                    $blueprint->dropColumn('created_at');
                }

                if (Schema::hasColumn($table, 'updated_at')) {
                    $blueprint->dropColumn('updated_at');
                }
            });
        }
    }

    private function copyLegacyTimestamp(string $table, string $standardColumn, ?string $legacyColumn): void
    {
        if ($legacyColumn === null || !Schema::hasColumn($table, $legacyColumn)) {
            return;
        }

        DB::table($table)
            ->whereNull($standardColumn)
            ->whereNotNull($legacyColumn)
            ->update([$standardColumn => DB::raw($legacyColumn)]);
    }
};
