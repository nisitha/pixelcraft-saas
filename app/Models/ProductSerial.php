<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSerial extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'invoice_id',
        'contact_id',
        'serial_number',
        'warranty_months',
        'warranty_expires_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'warranty_months' => 'integer',
            'warranty_expires_at' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function isWarrantyActive(): bool
    {
        if (! $this->warranty_expires_at) {
            return false;
        }

        return $this->warranty_expires_at->isFuture();
    }
}
