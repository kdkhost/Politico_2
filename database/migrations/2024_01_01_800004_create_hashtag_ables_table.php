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
        Schema::create('hashtag_ables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hashtag_id')->constrained('hashtags')->onDelete('cascade');
            $table->string('hashtag_able_type');
            $table->unsignedBigInteger('hashtag_able_id');
            $table->timestamp('new_created_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index(['hashtag_able_type', 'hashtag_able_id']);
            $table->index('hashtag_id');
            $table->comment('Relação polimórfica hashtags');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hashtag_ables');
    }
};
