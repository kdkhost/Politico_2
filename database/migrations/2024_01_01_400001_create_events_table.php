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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titulo');
            $table->string('slug');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();
            $table->string('endereco')->nullable();
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->string('cor', 9)->nullable();
            $table->string('icone')->nullable();
            $table->string('tipo')->default('publico');
            $table->boolean('all_day')->default(false);
            $table->json('recorrencia')->nullable();
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->json('participants')->nullable();
            $table->json('attachments')->nullable();
            $table->string('link_externo')->nullable();
            $table->boolean('publicado')->default(true);
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index(['data_inicio', 'data_fim']);
            $table->index('tipo');
            $table->comment('Eventos e agenda');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
