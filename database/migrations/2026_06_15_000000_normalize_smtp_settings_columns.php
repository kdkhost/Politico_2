<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('smtp_settings')) {
            return;
        }

        Schema::table('smtp_settings', function (Blueprint $table): void {
            if (!Schema::hasColumn('smtp_settings', 'mail_mailer')) {
                $table->string('mail_mailer')->nullable()->after('id');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_host')) {
                $table->string('mail_host')->nullable()->after('mail_mailer');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_port')) {
                $table->unsignedInteger('mail_port')->nullable()->after('mail_host');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_username')) {
                $table->string('mail_username')->nullable()->after('mail_port');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_password')) {
                $table->text('mail_password')->nullable()->after('mail_username');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_encryption')) {
                $table->string('mail_encryption')->nullable()->after('mail_password');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_from_address')) {
                $table->string('mail_from_address')->nullable()->after('mail_encryption');
            }

            if (!Schema::hasColumn('smtp_settings', 'mail_from_name')) {
                $table->string('mail_from_name')->nullable()->after('mail_from_address');
            }

            if (!Schema::hasColumn('smtp_settings', 'test_recipient')) {
                $table->string('test_recipient')->nullable()->after('ultimo_teste');
            }
        });

        DB::table('smtp_settings')->orderBy('id')->chunkById(100, function ($settings): void {
            foreach ($settings as $setting) {
                $legacyPort = (int) ($setting->porta ?? 587);

                DB::table('smtp_settings')
                    ->where('id', $setting->id)
                    ->update([
                        'mail_mailer' => $setting->mail_mailer ?: ($setting->mailer ?? 'smtp'),
                        'mail_host' => $setting->mail_host ?: ($setting->host ?? null),
                        'mail_port' => $setting->mail_port ?: ($legacyPort > 0 ? $legacyPort : 587),
                        'mail_username' => $setting->mail_username ?: ($setting->usuario ?? null),
                        'mail_password' => $setting->mail_password ?: ($setting->senha ?? null),
                        'mail_encryption' => $setting->mail_encryption ?: ($setting->criptografia ?? null),
                        'mail_from_address' => $setting->mail_from_address ?: ($setting->from_address ?? null),
                        'mail_from_name' => $setting->mail_from_name ?: ($setting->from_name ?? null),
                    ]);
            }
        });
    }

    public function down(): void
    {
        //
    }
};
