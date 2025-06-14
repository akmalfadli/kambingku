<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Goat extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_SOLD = 'sold';
    const STATUS_DECEASED = 'deceased';
    const STATUS_DEAD = 'dead';

    // Type constants
    const TYPE_BREEDING = 'breeding';
    const TYPE_FATTENING = 'fattening';

    protected $fillable = [
        'tag_number', 'name', 'breed', 'gender', 'date_of_birth',
        'status', 'type', 'origin', 'purchase_price', 'current_weight',
        'father_id', 'mother_id', 'notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'purchase_price' => 'decimal:2',
        'current_weight' => 'decimal:2',
    ];

    public function father(): BelongsTo
    {
        return $this->belongsTo(Goat::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Goat::class, 'mother_id');
    }

    public function offspring(): HasMany
    {
        return $this->hasMany(Goat::class, 'father_id')
                   ->orWhere('mother_id', $this->id);
    }

    public function matingRecordsAsMale(): HasMany
    {
        return $this->hasMany(MatingRecord::class, 'male_goat_id');
    }

    public function matingRecordsAsFemale(): HasMany
    {
        return $this->hasMany(MatingRecord::class, 'female_goat_id');
    }

    public function pregnancies(): HasMany
    {
        return $this->hasMany(Pregnancy::class, 'female_goat_id');
    }

    public function feedingLogs(): HasMany
    {
        return $this->hasMany(FeedingLog::class);
    }

    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    // Single sale relationship (if goat can only be sold once)
    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
              ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(300)
              ->height(300)
              ->sharpen(10);
    }

    // Helper methods
    public function getAgeAttribute(): string
    {
        if (!$this->date_of_birth) {
            return 'Tidak diketahui';
        }
        return $this->date_of_birth->diffForHumans(now(), true);
    }

    public function getAgeInMonthsAttribute(): int
    {
        if (!$this->date_of_birth) {
            return 0;
        }
        return $this->date_of_birth->diffInMonths(now());
    }

    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->sum('amount') +
               $this->feedingLogs()->sum('cost') +
               $this->healthRecords()->sum('cost') +
               ($this->purchase_price ?? 0);
    }

    public function getLatestWeightAttribute(): ?float
    {
        return $this->weightLogs()->latest('weigh_date')->value('weight') ?? $this->current_weight;
    }

    // New helper methods for profit calculation
    public function isFattening(): bool
    {
        return $this->type === self::TYPE_FATTENING;
    }

    public function isBreeding(): bool
    {
        return $this->type === self::TYPE_BREEDING;
    }

    public function getDisplayStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_SOLD => 'Terjual',
            self::STATUS_DECEASED, self::STATUS_DEAD => 'Mati',
            default => ucfirst($this->status),
        };
    }

    public function getFormattedPurchasePriceAttribute(): string
    {
        return $this->purchase_price ? \App\Helpers\CurrencyHelper::formatRupiah($this->purchase_price) : 'Tidak ada';
    }

    // Check if goat has been sold
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD || $this->sales()->exists();
    }

    // Get potential profit if sold at current market price
    public function getPotentialProfitAttribute(): float
    {
        // This would need market price logic - placeholder for now
        return 0;
    }
}
