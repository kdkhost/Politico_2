<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<int, array{table: string, columns: array<int, string>, name: string}> */
    private array $indexes = [
        ['table' => 'posts', 'columns' => ['status', 'created_at'], 'name' => 'idx_posts_status_created'],
        ['table' => 'posts', 'columns' => ['category_id', 'status', 'created_at'], 'name' => 'idx_posts_cat_status_created'],
        ['table' => 'posts', 'columns' => ['user_id', 'created_at'], 'name' => 'idx_posts_user_created'],
        ['table' => 'posts', 'columns' => ['deleted_at', 'created_at'], 'name' => 'idx_posts_deleted_created'],

        ['table' => 'pages', 'columns' => ['status', 'created_at'], 'name' => 'idx_pages_status_created'],
        ['table' => 'pages', 'columns' => ['user_id', 'created_at'], 'name' => 'idx_pages_user_created'],
        ['table' => 'pages', 'columns' => ['ordem', 'created_at'], 'name' => 'idx_pages_order_created'],
        ['table' => 'pages', 'columns' => ['deleted_at', 'created_at'], 'name' => 'idx_pages_deleted_created'],

        ['table' => 'users', 'columns' => ['status', 'created_at'], 'name' => 'idx_users_status_created'],
        ['table' => 'users', 'columns' => ['profile_id', 'status'], 'name' => 'idx_users_profile_status'],
        ['table' => 'users', 'columns' => ['is_blocked', 'status'], 'name' => 'idx_users_block_status'],
        ['table' => 'users', 'columns' => ['ultimo_acesso'], 'name' => 'idx_users_last_access'],

        ['table' => 'events', 'columns' => ['publicado', 'data_inicio'], 'name' => 'idx_events_public_start'],
        ['table' => 'events', 'columns' => ['status', 'data_inicio'], 'name' => 'idx_events_status_start'],
        ['table' => 'events', 'columns' => ['tipo', 'status', 'data_inicio'], 'name' => 'idx_events_type_status_start'],
        ['table' => 'events', 'columns' => ['deleted_at', 'data_inicio'], 'name' => 'idx_events_deleted_start'],

        ['table' => 'financial_transactions', 'columns' => ['status', 'data_pagamento'], 'name' => 'idx_fin_status_paid_at'],
        ['table' => 'financial_transactions', 'columns' => ['tipo', 'status', 'data_pagamento'], 'name' => 'idx_fin_type_status_paid_at'],
        ['table' => 'financial_transactions', 'columns' => ['categoria_id', 'status', 'data_vencimento'], 'name' => 'idx_fin_cat_status_due_at'],
        ['table' => 'financial_transactions', 'columns' => ['user_id', 'created_at'], 'name' => 'idx_fin_user_created'],
        ['table' => 'financial_transactions', 'columns' => ['deleted_at', 'data_vencimento'], 'name' => 'idx_fin_deleted_due_at'],

        ['table' => 'transparency_items', 'columns' => ['status', 'data_publicacao'], 'name' => 'idx_trans_status_pub_at'],
        ['table' => 'transparency_items', 'columns' => ['tipo', 'status', 'data_publicacao'], 'name' => 'idx_trans_type_status_pub_at'],
        ['table' => 'transparency_items', 'columns' => ['categoria', 'data_publicacao'], 'name' => 'idx_trans_cat_pub_at'],
        ['table' => 'transparency_items', 'columns' => ['data_referencia'], 'name' => 'idx_trans_reference_at'],

        ['table' => 'contacts', 'columns' => ['created_at'], 'name' => 'idx_contacts_created'],
        ['table' => 'contacts', 'columns' => ['lido', 'created_at'], 'name' => 'idx_contacts_read_created'],
        ['table' => 'contacts', 'columns' => ['respondido', 'created_at'], 'name' => 'idx_contacts_replied_created'],
        ['table' => 'contacts', 'columns' => ['email', 'created_at'], 'name' => 'idx_contacts_email_created'],

        ['table' => 'notifications', 'columns' => ['read_at', 'created_at'], 'name' => 'idx_notifications_read_created'],
        ['table' => 'notifications', 'columns' => ['notifiable_type', 'notifiable_id', 'read_at'], 'name' => 'idx_notifications_owner_read'],

        ['table' => 'logs', 'columns' => ['created_at'], 'name' => 'idx_logs_created'],
        ['table' => 'logs', 'columns' => ['acao', 'created_at'], 'name' => 'idx_logs_action_created'],
        ['table' => 'logs', 'columns' => ['user_id', 'created_at'], 'name' => 'idx_logs_user_created'],

        ['table' => 'visits', 'columns' => ['bot', 'visit_time'], 'name' => 'idx_visits_bot_time'],
        ['table' => 'visits', 'columns' => ['ip', 'visit_time'], 'name' => 'idx_visits_ip_time'],
        ['table' => 'visits', 'columns' => ['country', 'visit_time'], 'name' => 'idx_visits_country_time'],
        ['table' => 'visits', 'columns' => ['unique_visit', 'visit_time'], 'name' => 'idx_visits_unique_time'],

        ['table' => 'media', 'columns' => ['status', 'created_at'], 'name' => 'idx_media_status_created'],
        ['table' => 'media', 'columns' => ['pasta', 'tipo', 'status'], 'name' => 'idx_media_folder_type_status'],
        ['table' => 'media', 'columns' => ['user_id', 'created_at'], 'name' => 'idx_media_user_created'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $index) {
            $this->addIndexIfMissing($index['table'], $index['columns'], $index['name']);
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->indexes) as $index) {
            $this->dropIndexIfExists($index['table'], $index['name']);
        }
    }

    /**
     * @param array<int, string> $columns
     */
    private function addIndexIfMissing(string $table, array $columns, string $name): void
    {
        if (!$this->tableHasColumns($table, $columns) || $this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
            $blueprint->index($columns, $name);
        });
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($name): void {
            $blueprint->dropIndex($name);
        });
    }

    /**
     * @param array<int, string> $columns
     */
    private function tableHasColumns(string $table, array $columns): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $name): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('" . str_replace("'", "''", $table) . "')");

            foreach ($indexes as $index) {
                if (($index->name ?? null) === $name) {
                    return true;
                }
            }

            return false;
        }

        if ($driver === 'pgsql') {
            return DB::table('pg_indexes')
                ->where('tablename', $table)
                ->where('indexname', $name)
                ->exists();
        }

        $quotedTable = str_replace('`', '``', $table);

        return count(DB::select("SHOW INDEX FROM `{$quotedTable}` WHERE Key_name = ?", [$name])) > 0;
    }
};
