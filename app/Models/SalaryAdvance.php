<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'amount',
        'request_date',
        'deduct_month',
        'reason',
        'status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'request_date' => 'date',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
