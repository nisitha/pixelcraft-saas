<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'month',
        'title',
        'total_basic',
        'total_allowances',
        'total_gross',
        'total_epf_employee',
        'total_epf_employer',
        'total_etf_employer',
        'total_advances',
        'total_deductions',
        'total_net_payable',
        'status',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'total_basic' => 'decimal:2',
            'total_allowances' => 'decimal:2',
            'total_gross' => 'decimal:2',
            'total_epf_employee' => 'decimal:2',
            'total_epf_employer' => 'decimal:2',
            'total_etf_employer' => 'decimal:2',
            'total_advances' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'total_net_payable' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (class_exists(Filament::class) && ($tenant = Filament::getTenant())) {
                $builder->where($builder->qualifyColumn('company_id'), $tenant->id);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getTotalEpfEtfAttribute(): float
    {
        return round((float) $this->total_epf_employee + (float) $this->total_epf_employer + (float) $this->total_etf_employer, 2);
    }
}
