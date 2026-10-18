<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditCompanyProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Company Profile & Branding';
    }

    public static function canView(Model $tenant): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isTenantOwnerOrAdmin($tenant instanceof Company ? $tenant : null);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Company Settings')
                    ->tabs([
                        Tab::make('Company Identity & Registration')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Company Legal Name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('phone')
                                        ->label('Primary Phone Number(s)')
                                        ->tel()
                                        ->maxLength(60),

                                    TextInput::make('email')
                                        ->label('Official Email Address')
                                        ->email()
                                        ->maxLength(255),

                                    TextInput::make('tax_number')
                                        ->label('Tax / VAT / TIN Number')
                                        ->maxLength(50)
                                        ->placeholder('e.g. VAT-100293847'),

                                    Textarea::make('address')
                                        ->label('Business / Office Address')
                                        ->rows(2)
                                        ->columnSpanFull(),

                                    FileUpload::make('logo_path')
                                        ->label('Company Official Logo')
                                        ->disk('public')
                                        ->directory('company-logos')
                                        ->visibility('public')
                                        ->image()
                                        ->maxSize(2048)
                                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                        ->imageResizeMode('contain')
                                        ->imageResizeTargetWidth('600')
                                        ->imageResizeTargetHeight('300')
                                        ->imageResizeUpscale(false)
                                        ->columnSpanFull()
                                        ->helperText('Uploaded logo appears on live A4 previews, PDF downloads, and public customer share pages.'),
                                ]),
                            ]),

                        Tab::make('Advanced Branding & Document Theme')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Brand Colors & Document Styling')
                                    ->description('Customize the visual theme of your invoices, quotations, and public customer portal.')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            ColorPicker::make('settings.primary_color')
                                                ->label('Primary Brand Color')
                                                ->default('#047857')
                                                ->helperText('Used for document badges, totals, and primary titles.'),

                                            ColorPicker::make('settings.table_header_color')
                                                ->label('Table Header Color')
                                                ->default('#047857')
                                                ->helperText('Background color for the line items table header bar.'),

                                            ColorPicker::make('settings.text_color')
                                                ->label('Header Text Color')
                                                ->default('#ffffff')
                                                ->helperText('Text color on top of the table header bar.'),
                                        ]),

                                        Grid::make(2)->schema([
                                            TextInput::make('settings.quote_prefix')
                                                ->label('Quotation Number Prefix')
                                                ->placeholder('e.g. VHE/2026/ or QUO-'),

                                            TextInput::make('settings.invoice_prefix')
                                                ->label('Invoice Number Prefix')
                                                ->placeholder('e.g. INV-2026- or INV-'),
                                        ]),

                                        FileUpload::make('settings.signature_path')
                                            ->label('Authorized Digital Signature & Stamp')
                                            ->disk('public')
                                            ->directory('company-signatures')
                                            ->visibility('public')
                                            ->image()
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                            ->imageResizeMode('contain')
                                            ->imageResizeTargetWidth('400')
                                            ->imageResizeTargetHeight('200')
                                            ->imageResizeUpscale(false)
                                            ->helperText('Transparent PNG signature image stamped onto generated A4 documents and PDFs.'),
                                    ]),
                            ]),

                        Tab::make('Remittance, Terms & Document Layout')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Bank Remittance & Default Terms')
                                    ->schema([
                                        Textarea::make('settings.bank_details')
                                            ->label('Bank Account & Wire Remittance Details')
                                            ->placeholder("• DFCC Bank | Acc: 101001490231 (Branch: Giriulla)\n• Bank Of Ceylon (BOC) | Acc: 97194769 (Branch: Giriulla)")
                                            ->rows(3)
                                            ->helperText('Displayed at the bottom of customer invoices for direct wire or online bank transfers.'),

                                        Textarea::make('settings.default_notes')
                                            ->label('Default Customer Notes')
                                            ->placeholder('Thank you for considering Vertex Home Solutions for your solar energy investment.')
                                            ->rows(2),

                                        Textarea::make('settings.default_quote_terms')
                                            ->label('Default Quotation Terms & Conditions')
                                            ->placeholder("1. Valid for 07 days from the date of issue.\n2. 60% advance upon confirmation, 20% on delivery, 20% after commissioning.")
                                            ->rows(3),

                                        Textarea::make('settings.default_invoice_terms')
                                            ->label('Default Invoice Terms & Conditions')
                                            ->placeholder('Payment due within agreed timeframe. Goods remain property of seller until payment in full is cleared.')
                                            ->rows(2),

                                        TextInput::make('settings.pos_footer_note')
                                            ->label('POS Thermal Receipt Footer Note')
                                            ->placeholder('Thank you for your business! Please retain this receipt for warranty claims.'),
                                    ]),

                                Section::make('Document Block Display Toggles')
                                    ->description('Control which optional engineering sections appear on generated customer documents.')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Toggle::make('settings.show_solar_specs')
                                                ->label('Solar Technical Specs Block')
                                                ->default(true)
                                                ->helperText('Displays System kWp, Phase, Inverter, and Panel specs.'),

                                            Toggle::make('settings.show_ceb_account')
                                                ->label('CEB / LECO Account No')
                                                ->default(true)
                                                ->helperText('Displays customer utility connection reference.'),

                                            Toggle::make('settings.show_milestones')
                                                ->label('Milestone Payment Schedule')
                                                ->default(true)
                                                ->helperText('Displays 60% Advance / 20% Delivery / 20% Grid Commissioning.'),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (empty($data['logo_path']) && ! empty($data['settings']['logo_path'])) {
            $data['logo_path'] = $data['settings']['logo_path'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['logo_path'])) {
            if (is_array($data['logo_path'])) {
                $data['logo_path'] = array_values($data['logo_path'])[0] ?? null;
            }

            if (! empty($data['logo_path'])) {
                $this->optimizeUploadedImage($data['logo_path']);
            }
        }

        $currentSettings = $this->tenant instanceof Company ? ($this->tenant->settings ?? []) : [];
        if (! is_array($currentSettings)) {
            $currentSettings = [];
        }

        $formSettings = $data['settings'] ?? [];
        if (! is_array($formSettings)) {
            $formSettings = [];
        }

        $mergedSettings = array_merge($currentSettings, $formSettings);

        if (array_key_exists('logo_path', $data)) {
            $mergedSettings['logo_path'] = $data['logo_path'];
        }

        if (isset($mergedSettings['signature_path'])) {
            if (is_array($mergedSettings['signature_path'])) {
                $mergedSettings['signature_path'] = array_values($mergedSettings['signature_path'])[0] ?? null;
            }

            if (! empty($mergedSettings['signature_path'])) {
                $this->optimizeUploadedImage($mergedSettings['signature_path']);
            }
        }

        $data['settings'] = $mergedSettings;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (array_key_exists('logo_path', $data)) {
            $record->logo_path = $data['logo_path'];
        }

        if (array_key_exists('settings', $data)) {
            $record->settings = $data['settings'];
        }

        $record->fill($data);
        $record->save();

        return $record;
    }

    protected function optimizeUploadedImage(string $relativePath): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $fullPath = null;
        if (Storage::disk('public')->exists($relativePath)) {
            $fullPath = Storage::disk('public')->path($relativePath);
        } elseif (file_exists(storage_path('app/public/'.$relativePath))) {
            $fullPath = storage_path('app/public/'.$relativePath);
        } elseif (file_exists(public_path('storage/'.$relativePath))) {
            $fullPath = public_path('storage/'.$relativePath);
        }

        if (! $fullPath || ! is_file($fullPath)) {
            return;
        }

        $info = @getimagesize($fullPath);
        if (! $info) {
            return;
        }

        [$width, $height, $type] = $info;

        $maxWidth = 800;
        $maxHeight = 600;

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int) max(1, round($width * $ratio));
        $newHeight = (int) max(1, round($height * $ratio));

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => @imagecreatefrompng($fullPath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($fullPath),
            default => null,
        };

        if (! $source) {
            return;
        }

        $destination = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($destination, $fullPath, 85),
            IMAGETYPE_PNG => imagepng($destination, $fullPath, 8),
            IMAGETYPE_WEBP => imagewebp($destination, $fullPath, 85),
            default => null,
        };

        imagedestroy($source);
        imagedestroy($destination);
    }
}
