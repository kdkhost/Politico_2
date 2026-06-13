<?php

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
        Schema::create('waf_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45);
            $table->string('type');
            $table->text('reason')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('request_data')->nullable();
            $table->timestamp('new_created_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index('ip');
            $table->index('type');
            $table->comment('Logs do Web Application Firewall');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waf_logs');
    }
};
