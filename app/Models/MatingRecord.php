<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'male_goat_id', 'female_goat_id', 'mating_date',
        'expected_delivery_date', 'outcome', 'notes'
    ];

    protected $casts = [
        'mating_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function maleGoat(): BelongsTo
    {
        return $this->belongsTo(Goat::class, 'male_goat_id');
    }

    public function femaleGoat(): BelongsTo
    {
        return $this->belongsTo(Goat::class, 'female_goat_id');
    }

    public function pregnancy(): HasOne
    {
        return $this->hasOne(Pregnancy::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($matingRecord) {
            // Auto-calculate expected delivery date (150 days gestation period)
            $matingRecord->expected_delivery_date = $matingRecord->mating_date->addDays(150);
        });
    }
}
