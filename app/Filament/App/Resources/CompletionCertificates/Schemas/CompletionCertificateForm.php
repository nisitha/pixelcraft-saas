<?php

namespace App\Filament\App\Resources\CompletionCertificates\Schemas;

use App\Models\Contact;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CompletionCertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)
                    ->extraAttributes(['class' => 'able-pro-cert-studio-layout'])
                    ->schema([
                        // Left Column (55% Form Inputs): Sleek Able Pro Cards
                        Grid::make(1)
                            ->extraAttributes(['class' => 'able-pro-form-col'])
                            ->schema([
                                // Section 1: Document Format & Paper Setup
                                Section::make('Document Format & Paper Setup')
                                    ->description('Configure paper size, orientation, and official certificate registry.')
                                    ->icon(Heroicon::OutlinedDocumentText)
                                    ->extraAttributes(['class' => 'able-pro-card'])
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('paper_size')
                                                ->label('Paper Size')
                                                ->options([
                                                    'a4' => 'A4 (210 × 297 mm - Standard)',
                                                    'a5' => 'A5 (148 × 210 mm - Compact)',
                                                    'letter' => 'Letter (8.5 × 11 in)',
                                                    'b5' => 'B5 (176 × 250 mm)',
                                                ])
                                                ->default('a4')
                                                ->required()
                                                ->live(),

                                            Select::make('orientation')
                                                ->label('Orientation')
                                                ->options([
                                                    'landscape' => 'Landscape (Horizontal)',
                                                    'portrait' => 'Portrait (Vertical)',
                                                ])
                                                ->default('landscape')
                                                ->required()
                                                ->live(),
                                        ]),

                                        Grid::make(3)->schema([
                                            TextInput::make('certificate_number')
                                                ->label('Certificate No')
                                                ->default(fn () => 'CERT-VHE/'.date('Y').'/RU/'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT).'/'.str_pad((string) rand(1, 99), 3, '0', STR_PAD_LEFT))
                                                ->required()
                                                ->live(onBlur: true),

                                            DatePicker::make('commissioning_date')
                                                ->label('Commissioning Date')
                                                ->default(now())
                                                ->required()
                                                ->live(),

                                            Select::make('status')
                                                ->label('Status')
                                                ->options([
                                                    'draft' => 'Draft',
                                                    'issued' => 'Officially Issued',
                                                ])
                                                ->default('draft')
                                                ->required()
                                                ->live(),
                                        ]),
                                    ]),

                                // Section 2: Customer & Installation Site
                                Section::make('Customer & Installation Site')
                                    ->description('Customer identity, site premises, and electrical grid interconnection.')
                                    ->icon(Heroicon::OutlinedBuildingOffice2)
                                    ->extraAttributes(['class' => 'able-pro-card'])
                                    ->schema([
                                        Select::make('contact_id')
                                            ->label('Customer / Client Account')
                                            ->relationship('contact', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->helperText('Selecting a customer auto-fills the name on certificate and installation site address.')
                                            ->afterStateUpdated(function (Set $set, ?int $state) {
                                                if ($state && $contact = Contact::find($state)) {
                                                    $set('customer_name', strtoupper($contact->name));
                                                    if (! empty($contact->address)) {
                                                        $set('site_address', $contact->address);
                                                    }
                                                }
                                            }),

                                        Grid::make(2)->schema([
                                            TextInput::make('customer_name')
                                                ->label('Customer Name on Certificate')
                                                ->placeholder('e.g., MR. M A N A M Fernando')
                                                ->required()
                                                ->live(onBlur: true),

                                            Select::make('grid_connection')
                                                ->label('Grid Connection Type')
                                                ->options([
                                                    'On-Grid Net Metering' => 'On-Grid Net Metering',
                                                    'Net Accounting' => 'Net Accounting',
                                                    'Net Plus' => 'Net Plus',
                                                    'Off-Grid' => 'Off-Grid',
                                                    'Hybrid' => 'Hybrid',
                                                    'Non' => 'Non',
                                                ])
                                                ->default('On-Grid Net Metering')
                                                ->required()
                                                ->live(),
                                        ]),

                                        Textarea::make('site_address')
                                            ->label('Installation Site Address')
                                            ->rows(2)
                                            ->placeholder('e.g., 260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa')
                                            ->required()
                                            ->live(onBlur: true),
                                    ]),

                                // Section 3: Technical Specifications
                                Section::make('Technical Specifications')
                                    ->description('Solar power capacity, warranty coverage, operational condition, and testing compliance.')
                                    ->icon(Heroicon::OutlinedBolt)
                                    ->extraAttributes(['class' => 'able-pro-card'])
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('system_capacity')
                                                ->label('System Capacity (kWp & Battery)')
                                                ->default('05 kW+05kw Battery offgrid Solar System')
                                                ->placeholder('e.g., 10 kWp Grid-Tied Solar System')
                                                ->required()
                                                ->live(onBlur: true),

                                            TextInput::make('warranty_period')
                                                ->label('Warranty Period')
                                                ->default('30 Years (Panels) / 10 Years (Inverter+Battery)')
                                                ->required()
                                                ->live(onBlur: true),
                                        ]),

                                        Grid::make(2)->schema([
                                            TextInput::make('system_status')
                                                ->label('System Status')
                                                ->default('Commissioned & Operational')
                                                ->required()
                                                ->live(onBlur: true),

                                            TextInput::make('verification_status')
                                                ->label('Verification Standard')
                                                ->default('Engineering Safety Compliant')
                                                ->required()
                                                ->live(onBlur: true),
                                        ]),

                                        Textarea::make('compliance_note')
                                            ->label('Safety Compliance Note')
                                            ->rows(2)
                                            ->default('Installed, tested, and commissioned in accordance with standard safety regulations.')
                                            ->required()
                                            ->live(onBlur: true),
                                    ]),

                                // Section 4: Authorized Signatories
                                Section::make('Authorized Signatories')
                                    ->description('Engineering leads, corporate signatories, and execution date.')
                                    ->icon(Heroicon::OutlinedCheckBadge)
                                    ->extraAttributes(['class' => 'able-pro-card'])
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Fieldset::make('Lead Engineer')
                                                ->schema([
                                                    TextInput::make('lead_engineer_name')
                                                        ->label('Lead Engineer Name')
                                                        ->default('Lead Engineer')
                                                        ->required()
                                                        ->live(onBlur: true),

                                                    TextInput::make('lead_engineer_title')
                                                        ->label('Engineer Title')
                                                        ->default('Technical Operations')
                                                        ->required()
                                                        ->live(onBlur: true),
                                                ]),

                                            Fieldset::make('Managing Director')
                                                ->schema([
                                                    TextInput::make('director_name')
                                                        ->label('Director Name')
                                                        ->default('Managing Director')
                                                        ->required()
                                                        ->live(onBlur: true),

                                                    TextInput::make('director_title')
                                                        ->label('Director Title')
                                                        ->default('Vertex Home Solutions (Pvt) Ltd')
                                                        ->required()
                                                        ->live(onBlur: true),
                                                ]),
                                        ]),

                                        DatePicker::make('signed_date')
                                            ->label('Date of Execution')
                                            ->default(now())
                                            ->required()
                                            ->live(),
                                    ]),
                            ])
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 7,
                                'xl' => 7,
                            ]),

                        // Right Column (45% Sticky Preview Studio)
                        Grid::make(1)
                            ->extraAttributes(['class' => 'able-pro-preview-col'])
                            ->schema([
                                View::make('filament.app.resources.completion-certificates.live-preview'),
                            ])
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 5,
                                'xl' => 5,
                            ]),
                    ]),
            ]);
    }
}
