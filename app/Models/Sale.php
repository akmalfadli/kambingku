<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'goat_id',
        'sale_date',
        'buyer_name',
        'buyer_contact',
        'sale_price',
        'cost_price',
        'weight_at_sale',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight_at_sale' => 'decimal:2',
    ];

    // Relationship
    public function goat(): BelongsTo
    {
        return $this->belongsTo(Goat::class);
    }

    // Accessor for profit calculation
    public function getProfitAttribute(): float
    {
        return $this->sale_price - ($this->cost_price ?? 0);
    }

    // Accessor for formatted profit
    public function getFormattedProfitAttribute(): string
    {
        return \App\Helpers\CurrencyHelper::formatRupiah($this->profit);
    }

    // Accessor for profit percentage
    public function getProfitPercentageAttribute(): float
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        return ($this->profit / $this->cost_price) * 100;
    }

    // Boot method to automatically update goat status and cost price
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            // Auto-fill cost_price from goat's purchase_price if not set
            if (!$sale->cost_price && $sale->goat && $sale->goat->purchase_price) {
                $sale->cost_price = $sale->goat->purchase_price;
            }
        });

        static::created(function ($sale) {
            // Update goat status to sold when sale is created
            if ($sale->goat) {
                $sale->goat->update(['status' => 'sold']);
            }
        });

        static::deleted(function ($sale) {
            // Restore goat status to active when sale is deleted
            if ($sale->goat) {
                $sale->goat->update(['status' => 'active']);
            }
        });
    }
}
