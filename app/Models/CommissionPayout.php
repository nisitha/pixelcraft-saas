<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionPayout extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'sales_agent_id',
        'invoice_id',
        'voucher_number',
        'payout_date',
        'amount',
        'payment_method',
        'reference_no',
        'status',
        'notes',
        'paper_size',
        'orientation',
    ];

    protected function casts(): array
    {
        return [
            'payout_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function salesAgent(): BelongsTo
    {
        return $this->belongsTo(SalesAgent::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
