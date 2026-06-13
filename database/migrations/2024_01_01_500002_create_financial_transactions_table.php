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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('categoria_id')->nullable()->constrained('financial_categories')->onDelete('set null');
            $table->string('tipo');
            $table->text('descricao')->nullable();
            $table->decimal('valor', 15, 2)->default(0);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->string('status')->default('pending');
            $table->string('comprovante')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index(['status', 'data_vencimento']);
            $table->index('tipo');
            $table->comment('Transações financeiras');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
