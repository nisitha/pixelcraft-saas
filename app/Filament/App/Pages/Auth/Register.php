<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SensitiveParameter;

class Register extends BaseRegister
{
    protected static bool $isDiscovered = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Full Name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email Address')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::min(8))
            ->showAllValidationMessages()
            ->dehydrateStateUsing(fn (#[SensitiveParameter] $state) => Hash::make($state))
            ->same('password_confirmation')
            ->validationAttribute('password');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->label('Confirm Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    public function updated($property): void
    {
        if (method_exists(parent::class, 'updated')) {
            parent::updated($property);
        }

        if ($property === 'data.passwordConfirmation' && ! isset($this->data['password_confirmation'])) {
            $this->data['password_confirmation'] = $this->data['passwordConfirmation'];
        }

        if ($property === 'data.password_confirmation' && ! isset($this->data['passwordConfirmation'])) {
            $this->data['passwordConfirmation'] = $this->data['password_confirmation'];
        }
    }

    public function beforeValidate(): void
    {
        if (isset($this->data['passwordConfirmation']) && ! isset($this->data['password_confirmation'])) {
            $this->data['password_confirmation'] = $this->data['passwordConfirmation'];
        }

        if (isset($this->data['password_confirmation']) && ! isset($this->data['passwordConfirmation'])) {
            $this->data['passwordConfirmation'] = $this->data['password_confirmation'];
        }
    }

    protected function rateLimit($maxAttempts, $decaySeconds = 60, $method = null, $component = null)
    {
        if (app()->runningUnitTests()) {
            return;
        }

        parent::rateLimit($maxAttempts, $decaySeconds, $method, $component);
    }

    protected function isRegisterRateLimited(string $email): bool
    {
        if (app()->runningUnitTests()) {
            return false;
        }

        return parent::isRegisterRateLimited($email);
    }
}
