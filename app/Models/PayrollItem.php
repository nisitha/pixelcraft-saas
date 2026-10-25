<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Services\PayrollCalculationService;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'payroll_id',
        'employee_id',
        'basic_salary',
        'fixed_allowance',
        'additional_allowance',
        'gross_salary',
        'epf_employee',
        'epf_employer',
        'etf_employer',
        'advance_deduction',
        'other_deductions',
        'total_deductions',
        'net_salary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'fixed_allowance' => 'decimal:2',
            'additional_allowance' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'epf_employee' => 'decimal:2',
            'epf_employer' => 'decimal:2',
            'etf_employer' => 'decimal:2',
            'advance_deduction' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (class_exists(Filament::class) && ($tenant = Filament::getTenant())) {
                $builder->where($builder->qualifyColumn('company_id'), $tenant->id);
            }
        });

        static::saving(function (PayrollItem $item) {
            $basic = (float) ($item->basic_salary ?? 0);
            $fixed = (float) ($item->fixed_allowance ?? 0);
            $addl = (float) ($item->additional_allowance ?? 0);
            $adv = (float) ($item->advance_deduction ?? 0);
            $other = (float) ($item->other_deductions ?? 0);

            $item->gross_salary = round($basic + $fixed + $addl, 2);
            $item->epf_employee = round($basic * 0.08, 2);
            $item->epf_employer = round($basic * 0.12, 2);
            $item->etf_employer = round($basic * 0.03, 2);
            $item->total_deductions = round($item->epf_employee + $adv + $other, 2);
            $item->net_salary = round($item->gross_salary - $item->total_deductions, 2);
        });

        static::saved(function (PayrollItem $item) {
            if ($item->payroll) {
                app(PayrollCalculationService::class)->recalculateTotals($item->payroll);
            }
        });

        static::deleted(function (PayrollItem $item) {
            if ($item->payroll) {
                app(PayrollCalculationService::class)->recalculateTotals($item->payroll);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
