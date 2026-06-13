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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->string('titulo');
            $table->string('url')->nullable();
            $table->string('icone')->nullable();
            $table->string('target')->default('_self');
            $table->string('route')->nullable();
            $table->json('params')->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('active')->default(true);
            $table->string('permission')->nullable();
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index('menu_id');
            $table->comment('Itens de menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
