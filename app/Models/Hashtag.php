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
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Hashtag extends Model
{
    protected $fillable = [
        'nome',
        'slug',
        'tipo',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'usage_count' => 'integer',
        ];
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'hashtaggable');
    }

    public function pages(): MorphToMany
    {
        return $this->morphedByMany(Page::class, 'hashtaggable');
    }
}
