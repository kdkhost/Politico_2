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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('slug');
            $table->text('descricao')->nullable();
            $table->string('icone')->nullable();
            $table->string('versao')->default('1.0.0');
            $table->boolean('active')->default(true);
            $table->integer('ordem')->default(0);
            $table->json('configuracoes')->nullable();
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->comment('Módulos do sistema CMS');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
