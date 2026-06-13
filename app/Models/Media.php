<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nome',
        'nome_original',
        'caminho',
        'url',
        'tipo',
        'mime_type',
        'extensao',
        'tamanho',
        'dimensoes',
        'alt_text',
        'descricao',
        'pasta',
        'tags',
        'status',
        'hash_arquivo',
        'downloadable',
    ];

    protected function casts(): array
    {
        return [
            'dimensoes' => 'json',
            'tags' => 'json',
            'downloadable' => 'bool',
            'tamanho' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usages(): MorphMany
    {
        return $this->hasMany(MediaUsage::class, 'media_id');
    }
}
