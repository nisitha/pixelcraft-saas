<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'calling_name',
        'nic_number',
        'designation',
        'contact_number',
        'daily_rate',
        'basic_salary',
        'fixed_allowance',
        'epf_number',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'joined_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'daily_rate' => 'decimal:2',
            'basic_salary' => 'decimal:2',
            'fixed_allowance' => 'decimal:2',
            'joined_date' => 'date',
            'is_active' => 'boolean',
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

    public function salaryAdvances(): HasMany
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }
}
