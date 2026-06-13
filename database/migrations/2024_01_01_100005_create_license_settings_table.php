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
        Schema::create('license_settings', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->nullable();
            $table->string('cliente')->nullable();
            $table->string('email_cliente')->nullable();
            $table->string('status')->default('inactive');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('next_verified_at')->nullable();
            $table->string('current_version')->nullable();
            $table->string('latest_version')->nullable();
            $table->boolean('update_available')->default(false);
            $table->json('license_data')->nullable();
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Configurações de licenciamento do sistema');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_settings');
    }
};
