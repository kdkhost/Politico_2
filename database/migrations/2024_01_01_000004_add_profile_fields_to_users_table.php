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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->onDelete('set null');
            $table->boolean('is_super_admin')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->string('avatar')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('cargo')->nullable();
            $table->timestamp('ultimo_acesso')->nullable();
            $table->string('ip_acesso', 45)->nullable();
            $table->json('preferencias')->nullable();
            $table->string('status')->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropColumn([
                'profile_id',
                'is_super_admin',
                'is_blocked',
                'avatar',
                'telefone',
                'cargo',
                'ultimo_acesso',
                'ip_acesso',
                'preferencias',
                'status',
            ]);
        });
    }
};
