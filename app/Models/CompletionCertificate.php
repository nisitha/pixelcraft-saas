<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletionCertificate extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'contact_id',
        'invoice_id',
        'quotation_id',
        'certificate_number',
        'customer_name',
        'system_capacity',
        'site_address',
        'commissioning_date',
        'grid_connection',
        'warranty_period',
        'system_status',
        'verification_status',
        'compliance_note',
        'lead_engineer_name',
        'lead_engineer_title',
        'director_name',
        'director_title',
        'signed_date',
        'paper_size',
        'orientation',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'commissioning_date' => 'date',
            'signed_date' => 'date',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
