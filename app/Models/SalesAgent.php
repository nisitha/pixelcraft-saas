<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesAgent extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'agent_code',
        'name',
        'nic_number',
        'phone',
        'email',
        'address',
        'bank_name',
        'bank_branch',
        'bank_account_name',
        'bank_account_no',
        'default_commission_type',
        'default_commission_value',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'default_commission_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function getTotalProjectsReferredAttribute(): int
    {
        return $this->invoices()->count();
    }

    public function getTotalCommissionEarnedAttribute(): float
    {
        return (float) $this->invoices()
            ->where('status', '!=', 'cancelled')
            ->sum('commission_amount');
    }

    public function getTotalCommissionPaidAttribute(): float
    {
        return (float) $this->payouts()
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getUnpaidBalanceAttribute(): float
    {
        return round($this->total_commission_earned - $this->total_commission_paid, 2);
    }

    public function calculateCommission(float $baseAmount): float
    {
        if ($this->default_commission_type === 'percentage') {
            return round(($baseAmount * (float) $this->default_commission_value) / 100, 2);
        }

        return (float) $this->default_commission_value;
    }
}
