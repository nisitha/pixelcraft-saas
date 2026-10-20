<?php

namespace App\Filament\App\Resources\TeamMembers\Pages;

use App\Filament\App\Resources\TeamMembers\TeamMemberResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tenant = Filament::getTenant();
        $data['role'] = $this->record->roleInTenant($tenant) ?? 'sales_executive';

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $tenant = Filament::getTenant();
        $role = $data['role'] ?? 'sales_executive';
        unset($data['role']);

        $updateData = [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $record->update($updateData);

        if ($tenant) {
            $tenant->users()->syncWithoutDetaching([
                $record->id => ['role' => $role],
            ]);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Remove from Team')
                ->action(function () {
                    $tenant = Filament::getTenant();
                    $currentUser = Filament::auth()->user();

                    if ($this->record->id === $currentUser->id) {
                        $ownerCount = $tenant->users()->wherePivot('role', 'owner')->count();
                        if ($ownerCount <= 1) {
                            Notification::make()
                                ->title('Cannot Remove Sole Owner')
                                ->body('You are the only owner of this company. Assign another owner before leaving.')
                                ->danger()
                                ->send();

                            return;
                        }
                    }

                    $tenant->users()->detach($this->record->id);

                    Notification::make()
                        ->title("{$this->record->name} removed from team")
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
