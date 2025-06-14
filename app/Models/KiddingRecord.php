<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KiddingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'pregnancy_id', 'mother_goat_id', 'delivery_date',
        'number_of_kids', 'kids_details', 'delivery_notes'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'kids_details' => 'array',
    ];

    public function pregnancy(): BelongsTo
    {
        return $this->belongsTo(Pregnancy::class);
    }

    public function motherGoat(): BelongsTo
    {
        return $this->belongsTo(Goat::class, 'mother_goat_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($kiddingRecord) {
            // Auto-register kids to main goat table
            foreach ($kiddingRecord->kids_details as $kid) {
                if ($kid['survival_status'] === 'alive') {
                    Goat::create([
                        'tag_number' => $kid['tag_id'],
                        'gender' => $kid['gender'],
                        'date_of_birth' => $kiddingRecord->delivery_date,
                        'status' => 'active',
                        'type' => 'breeding', // Default for newborns
                        'origin' => 'born',
                        'mother_id' => $kiddingRecord->mother_goat_id,
                        'father_id' => $kiddingRecord->pregnancy->matingRecord->male_goat_id,
                        'breed' => $kiddingRecord->motherGoat->breed,
                    ]);
                }
            }
        });
    }
}
