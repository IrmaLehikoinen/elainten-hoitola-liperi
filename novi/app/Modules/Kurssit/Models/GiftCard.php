<?php

namespace App\Modules\Kurssit\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'code',
        'source',
        'initial_amount',
        'balance',
        'valid_until',
        'purchaser_name',
        'purchaser_email',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function isExpired(): bool
    {
        return $this->valid_until !== null && $this->valid_until->isPast();
    }

    public function isUsable(): bool
    {
        return (float) $this->balance > 0 && ! $this->isExpired();
    }

    /**
     * Seuraava vapaa koodi annetulle lähteelle: S-alkuiset paikan päällä
     * myydyt kortit, V-alkuiset verkossa ostetut. Numero jatkuu aina
     * suurimmasta jo käytössä olevasta, ei kovaa 100 kappaleen kattoa.
     */
    public static function generateCode(string $source): string
    {
        $prefix = $source === 'online' ? 'V' : 'S';

        $lastNumber = static::where('code', 'like', $prefix.'%')
            ->get()
            ->map(fn ($card) => (int) substr($card->code, 1))
            ->max();

        $next = ($lastNumber ?? 0) + 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}