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
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'titulo',
        'slug',
        'descricao',
        'local',
        'endereco',
        'latitude',
        'longitude',
        'data_inicio',
        'data_fim',
        'cor',
        'icone',
        'tipo',
        'all_day',
        'recorrencia',
        'categoria_id',
        'status',
        'image',
        'participants',
        'attachments',
        'link_externo',
        'publicado',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
            'all_day' => 'bool',
            'recorrencia' => 'json',
            'participants' => 'json',
            'attachments' => 'json',
            'publicado' => 'bool',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
