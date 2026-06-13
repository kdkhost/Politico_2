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
        Schema::table('newsletter_subscribers', function (Blueprint $table): void {
            if (!Schema::hasColumn('newsletter_subscribers', 'confirmation_expires_at')) {
                $table->timestamp('confirmation_expires_at')->nullable()->after('subscribed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table): void {
            if (Schema::hasColumn('newsletter_subscribers', 'confirmation_expires_at')) {
                $table->dropColumn('confirmation_expires_at');
            }
        });
    }
};
