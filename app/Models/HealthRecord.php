<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'goat_id', 'record_date', 'diagnosis', 'treatment',
        'medicine_given', 'cost', 'vet_name', 'next_checkup_date', 'notes'
    ];

    protected $casts = [
        'record_date' => 'date',
        'next_checkup_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function goat(): BelongsTo
    {
        return $this->belongsTo(Goat::class);
    }
}
