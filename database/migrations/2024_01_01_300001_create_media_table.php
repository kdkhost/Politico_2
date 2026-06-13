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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nome');
            $table->string('nome_original');
            $table->string('caminho');
            $table->string('url')->nullable();
            $table->string('tipo')->default('other');
            $table->string('mime_type')->nullable();
            $table->string('extensao', 10)->nullable();
            $table->bigInteger('tamanho')->default(0);
            $table->json('dimensoes')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('descricao')->nullable();
            $table->string('pasta')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('active');
            $table->string('hash_arquivo')->unique();
            $table->boolean('downloadable')->default(true);
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index(['tipo', 'status']);
            $table->comment('Arquivos de mídia enviados');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
