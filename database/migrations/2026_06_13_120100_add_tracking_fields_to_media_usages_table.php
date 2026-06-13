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
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_usages', function (Blueprint $table): void {
            if (!Schema::hasColumn('media_usages', 'context')) {
                $table->string('context', 120)->nullable()->after('colecao');
            }

            if (!Schema::hasColumn('media_usages', 'field')) {
                $table->string('field', 120)->nullable()->after('context');
            }

            if (!Schema::hasColumn('media_usages', 'url')) {
                $table->string('url', 500)->nullable()->after('field');
            }

            if (!Schema::hasColumn('media_usages', 'metadata')) {
                $table->json('metadata')->nullable()->after('url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media_usages', function (Blueprint $table): void {
            $drops = [];

            foreach (['context', 'field', 'url', 'metadata'] as $column) {
                if (Schema::hasColumn('media_usages', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
