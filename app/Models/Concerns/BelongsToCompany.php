<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::creating(function ($model) {
            if (empty($model->company_id) && class_exists(Filament::class)) {
                $tenant = Filament::getTenant();
                if ($tenant instanceof Company) {
                    $model->company_id = $tenant->id;
                }
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
