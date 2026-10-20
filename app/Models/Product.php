<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'sku',
        'type',
        'unit_price',
        'cost_price',
        'stock_qty',
        'alert_qty',
        'track_serial',
        'track_inventory',
        'warranty_months',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock_qty' => 'decimal:2',
            'alert_qty' => 'decimal:2',
            'track_serial' => 'boolean',
            'track_inventory' => 'boolean',
            'warranty_months' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function serials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function getFormattedWarrantyAttribute(): ?string
    {
        if (! $this->warranty_months || $this->warranty_months <= 0) {
            return null;
        }

        if ($this->warranty_months % 12 === 0) {
            $years = (int) ($this->warranty_months / 12);

            return $years === 1 ? '1 Year Warranty' : "{$years} Years Warranty";
        }

        return "{$this->warranty_months} Months Warranty";
    }
}
