<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function getFilamentAvatarUrl(): ?string
    {
        return Filament::getTenant()?->getFilamentAvatarUrl();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function roleInTenant(?Company $tenant = null): ?string
    {
        if ($this->is_super_admin) {
            return 'owner';
        }

        $tenant ??= Filament::getTenant();
        if (! $tenant) {
            return null;
        }

        $membership = $this->companies()->where('companies.id', $tenant->id)->first();

        return $membership?->pivot?->role;
    }

    public function isTenantOwner(?Company $tenant = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->roleInTenant($tenant) === 'owner';
    }

    public function isTenantAdmin(?Company $tenant = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->roleInTenant($tenant) === 'admin';
    }

    public function isTenantOwnerOrAdmin(?Company $tenant = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $role = $this->roleInTenant($tenant);

        return in_array($role, ['owner', 'admin']);
    }

    public function isTenantSalesExecutive(?Company $tenant = null): bool
    {
        if ($this->is_super_admin) {
            return false;
        }

        return $this->roleInTenant($tenant) === 'sales_executive';
    }

    public function isTenantCashier(?Company $tenant = null): bool
    {
        if ($this->is_super_admin) {
            return false;
        }

        return $this->roleInTenant($tenant) === 'cashier';
    }

    public function isTenantEngineer(?Company $tenant = null): bool
    {
        if ($this->is_super_admin) {
            return false;
        }

        return $this->roleInTenant($tenant) === 'engineer';
    }

    public function canViewFinancialProfitAndCost(?Company $tenant = null): bool
    {
        return $this->isTenantOwnerOrAdmin($tenant);
    }

    public function canDeleteTenantRecords(?Company $tenant = null): bool
    {
        return $this->isTenantOwnerOrAdmin($tenant);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($panel->getId() === 'super-admin' || $panel->getId() === 'admin') {
            return (bool) $this->is_super_admin;
        }

        if ($panel->getId() === 'app') {
            return (bool) $this->is_active;
        }

        return true;
    }

    /**
     * @return array<Model> | Collection
     */
    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->is_super_admin && $this->companies->isEmpty()) {
            return Company::all();
        }

        return $this->companies;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->companies->contains($tenant);
    }
}
