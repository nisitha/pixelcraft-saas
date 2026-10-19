<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'contact_id',
        'quotation_id',
        'sales_agent_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'type',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'paid_amount',
        'due_amount',
        'commission_amount',
        'commission_status',
        'status',
        'is_sent',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function salesAgent(): BelongsTo
    {
        return $this->belongsTo(SalesAgent::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function serials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function commissionPayouts(): HasMany
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function completionCertificates(): HasMany
    {
        return $this->hasMany(CompletionCertificate::class);
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            $cost = (float) ($item->cost_price ?? 0);
            $qty = (float) ($item->quantity ?? 1);

            return $cost * $qty;
        });
    }

    public function getGrossProfitAttribute(): float
    {
        $netRevenue = (float) $this->grand_total - (float) $this->tax;

        return round($netRevenue - $this->total_cost, 2);
    }

    public function getNetProfitAttribute(): float
    {
        return round($this->gross_profit - (float) ($this->commission_amount ?? 0), 2);
    }

    public function getProfitMarginPercentageAttribute(): float
    {
        $grandTotal = (float) $this->grand_total;
        if ($grandTotal <= 0) {
            return 0.0;
        }

        return round(($this->net_profit / $grandTotal) * 100, 2);
    }
}
