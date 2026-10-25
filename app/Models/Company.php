<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Company extends Model implements HasAvatar, HasName
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'plan_id',
        'subscription_ends_at',
        'is_active',
        'phone',
        'email',
        'address',
        'logo_path',
        'tax_number',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'subscription_ends_at' => 'datetime',
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $path = $this->logo_path ?? ($this->settings['logo_path'] ?? null);

        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $url = $path;
        } else {
            $url = Storage::disk('public')->url($path);
        }

        if ($this->updated_at) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator.'v='.$this->updated_at->timestamp;
        }

        return $url;
    }

    public function getBankAccountsList(): array
    {
        $settings = $this->settings ?? [];
        if (! empty($settings['bank_accounts']) && is_array($settings['bank_accounts'])) {
            $valid = array_filter($settings['bank_accounts'], fn ($acc) => is_array($acc) && (! empty($acc['bank']) || ! empty($acc['account_no'])));
            if (! empty($valid)) {
                return array_values($valid);
            }
        }

        $raw = $settings['bank_details'] ?? '';
        if (empty($raw)) {
            return [];
        }

        // Automatic parsing if existing data uses | or newlines
        $lines = preg_split('/[\r\n]+/', trim($raw));
        $accounts = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));
            $bank = '';
            $accountName = '';
            $accountNo = '';
            $branch = '';

            foreach ($parts as $part) {
                if (preg_match('/^(?:Bank\s*(?:Account\s*\d+)?:\s*|Bank Name:\s*)(.+)$/i', $part, $m)) {
                    $bank = trim($m[1]);
                } elseif (preg_match('/^(?:Acc(?:ount)?\s*Name:\s*)(.+)$/i', $part, $m)) {
                    $accountName = trim($m[1]);
                } elseif (preg_match('/^(?:Acc(?:ount)?\s*(?:No|Number)?:\s*)(.+)$/i', $part, $m)) {
                    $accountNo = trim($m[1]);
                } elseif (preg_match('/^(?:Branch:\s*)(.+)$/i', $part, $m)) {
                    $branch = trim($m[1]);
                }
            }

            if (empty($bank) && count($parts) >= 1) {
                $bank = trim(preg_replace('/^Bank\s*(?:Account\s*\d+)?:\s*/i', '', $parts[0]));
            }
            if (empty($accountName) && count($parts) >= 2) {
                $accountName = trim(preg_replace('/^Acc(?:ount)?\s*Name:\s*/i', '', $parts[1]));
            }
            if (empty($accountNo) && count($parts) >= 3) {
                $accountNo = trim(preg_replace('/^Acc(?:ount)?\s*(?:No|Number)?:\s*/i', '', $parts[2]));
            }
            if (empty($branch) && count($parts) >= 4) {
                $branch = trim(preg_replace('/^Branch:\s*/i', '', $parts[3]));
            }

            if (! empty($bank) || ! empty($accountNo)) {
                $accounts[] = [
                    'bank' => $bank ?: 'Bank Remittance',
                    'account_name' => $accountName ?: $this->name,
                    'account_no' => $accountNo ?: '',
                    'branch' => $branch ?: '',
                ];
            }
        }

        return $accounts;
    }

    public function isSubscriptionActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->subscription_ends_at !== null && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function canAddMoreUsers(): bool
    {
        if (! $this->plan || ! $this->plan->max_users) {
            return true;
        }

        return $this->users()->count() < $this->plan->max_users;
    }

    public function getMaxUsersLimit(): int
    {
        return $this->plan?->max_users ?? 5;
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function productSerials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function subscriptionRequests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    public function completionCertificates(): HasMany
    {
        return $this->hasMany(CompletionCertificate::class);
    }

    public function salesAgents(): HasMany
    {
        return $this->hasMany(SalesAgent::class);
    }

    public function commissionPayouts(): HasMany
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function salaryAdvances(): HasMany
    {
        return $this->hasMany(SalaryAdvance::class);
    }
}
