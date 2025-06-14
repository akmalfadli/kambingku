<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregnancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'female_goat_id', 'mating_record_id', 'start_date',
        'expected_delivery_date', 'actual_delivery_date',
        'status', 'health_notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
    ];

    public function femaleGoat(): BelongsTo
    {
        return $this->belongsTo(Goat::class, 'female_goat_id');
    }

    public function matingRecord(): BelongsTo
    {
        return $this->belongsTo(MatingRecord::class);
    }

    public function kiddingRecords(): HasMany
    {
        return $this->hasMany(KiddingRecord::class);
    }
}
