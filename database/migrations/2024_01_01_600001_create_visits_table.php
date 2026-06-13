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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('page_url');
            $table->string('page_type')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('referrer_source')->nullable();
            $table->dateTime('visit_time');
            $table->string('session_id')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->boolean('unique_visit')->default(false);
            $table->boolean('bot')->default(false);
            $table->timestamp('new_created_at')->nullable();
            $table->timestamp('new_updated_at')->nullable();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->index('page_url');
            $table->index('visit_time');
            $table->index('session_id');
            $table->index(['page_type', 'page_id']);
            $table->comment('Registro de visitas ao site');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
