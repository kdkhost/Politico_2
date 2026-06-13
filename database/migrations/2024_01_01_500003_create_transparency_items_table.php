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
        Schema::create('transparency_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tipo');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->decimal('valor', 15, 2)->default(0);
            $table->date('data_publicacao')->nullable();
            $table->date('data_referencia')->nullable();
            $table->string('categoria')->nullable();
            $table->string('fornecedor')->nullable();
            $table->string('documento_numero')->nullable();
            $table->string('orgao_responsavel')->nullable();
            $table->json('arquivos')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index(['tipo', 'status']);
            $table->index('data_publicacao');
            $table->comment('Itens de transparência pública');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transparency_items');
    }
};
