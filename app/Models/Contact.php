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

class Contact extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'assunto',
        'mensagem',
        'lido',
        'respondido',
        'resposta',
        'responded_by',
        'responded_at',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'lido' => 'bool',
            'respondido' => 'bool',
            'responded_at' => 'datetime',
        ];
    }

    public function scopeNaoLido($query)
    {
        return $query->where('lido', false);
    }

    public function scopeRespondido($query)
    {
        return $query->where('respondido', true);
    }
}
