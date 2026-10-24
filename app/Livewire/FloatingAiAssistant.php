<?php

namespace App\Livewire;

use App\Services\GeminiAssistantService;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FloatingAiAssistant extends Component
{
    public bool $isOpen = false;

    public string $input = '';

    /**
     * @var array<int, array{role: string, content: string}>
     */
    public array $messages = [];

    public bool $isLoading = false;

    public function mount(): void
    {
        if (empty($this->messages)) {
            $this->messages = [
                [
                    'role' => 'assistant',
                    'content' => $this->getGreeting(),
                ],
            ];
        }
    }

    public function getGreeting(): string
    {
        $hour = (int) now()->format('H');

        if ($hour < 12) {
            return 'Good morning! I am Pixel Craft Studios AI Assistant. How can I assist you with your projects, invoices, or finances today?';
        }

        if ($hour < 17) {
            return 'Good afternoon! I am Pixel Craft Studios AI Assistant. How can I assist you with your projects, invoices, or finances today?';
        }

        return 'Good evening! I am Pixel Craft Studios AI Assistant. How can I assist you with your projects, invoices, or finances today?';
    }

    public function toggleOpen(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function clearChat(): void
    {
        $this->messages = [
            [
                'role' => 'assistant',
                'content' => $this->getGreeting(),
            ],
        ];
        $this->input = '';
        $this->isLoading = false;
    }

    public function sendMessage(?string $text = null): void
    {
        $query = trim($text ?? $this->input);

        if (empty($query)) {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'content' => $query,
        ];

        $this->input = '';
        $this->isLoading = true;

        $tenantId = Filament::getTenant()?->id ?? auth()->user()?->current_company_id ?? 1;

        try {
            $service = app(GeminiAssistantService::class);
            $response = $service->chat($this->messages, (int) $tenantId);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response,
            ];
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            if (str_contains($errorMsg, 'cURL error 28') || str_contains(strtolower($errorMsg), 'timed out')) {
                $userFriendlyMessage = 'The request timed out while communicating with the AI service. Please try asking again or make your query more specific.';
            } else {
                $userFriendlyMessage = 'Sorry, I encountered an issue processing your request: '.$errorMsg;
            }

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $userFriendlyMessage,
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function render(): View
    {
        return view('livewire.floating-ai-assistant');
    }
}
