<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'goat_id', 'feed_type', 'quantity', 'unit', 'cost',
        'feeding_date', 'is_group_feeding', 'goat_ids', 'notes'
    ];

    protected $casts = [
        'feeding_date' => 'date',
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_group_feeding' => 'boolean',
        'goat_ids' => 'array',
    ];

    public function goat(): BelongsTo
    {
        return $this->belongsTo(Goat::class);
    }
}
