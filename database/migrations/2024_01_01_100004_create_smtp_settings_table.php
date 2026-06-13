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
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mailer')->default('smtp');
            $table->string('host')->nullable();
            $table->string('porta', 10)->nullable();
            $table->string('usuario')->nullable();
            $table->text('senha')->nullable();
            $table->string('criptografia')->nullable();
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->string('reply_to')->nullable();
            $table->integer('timeout')->default(30);
            $table->boolean('debug')->default(false);
            $table->boolean('active')->default(false);
            $table->boolean('is_configured')->default(false);
            $table->timestamp('ultimo_teste')->nullable();
            $table->string('status_conexao')->nullable();
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Configurações de e-mail SMTP');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
