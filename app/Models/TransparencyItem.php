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

class TransparencyItem extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'descricao',
        'valor',
        'data_publicacao',
        'data_referencia',
        'categoria',
        'fornecedor',
        'documento_numero',
        'orgao_responsavel',
        'arquivos',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_publicacao' => 'date',
            'data_referencia' => 'date',
            'arquivos' => 'json',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
