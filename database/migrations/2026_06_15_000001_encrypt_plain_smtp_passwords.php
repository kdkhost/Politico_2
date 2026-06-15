<?php

declare(strict_types=1);

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('smtp_settings') || !Schema::hasColumn('smtp_settings', 'mail_password')) {
            return;
        }

        DB::table('smtp_settings')
            ->whereNotNull('mail_password')
            ->where('mail_password', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($settings): void {
                foreach ($settings as $setting) {
                    try {
                        Crypt::decryptString((string) $setting->mail_password);
                    } catch (DecryptException) {
                        DB::table('smtp_settings')
                            ->where('id', $setting->id)
                            ->update(['mail_password' => Crypt::encryptString((string) $setting->mail_password)]);
                    }
                }
            });
    }

    public function down(): void
    {
        //
    }
};
