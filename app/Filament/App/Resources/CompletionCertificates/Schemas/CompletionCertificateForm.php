<?php

namespace App\Filament\App\Resources\CompletionCertificates\Schemas;

use App\Models\Contact;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class CompletionCertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)->schema([
                    // Left Column (5 columns on desktop): Interactive Form Inputs
                    Grid::make(1)->schema([
                        Section::make('Format & Document Setup')
                            ->description('Configure paper size & orientation with live aspect ratio scaling')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('paper_size')
                                        ->label('Paper Size')
                                        ->options([
                                            'a4' => 'A4 (Standard 210 × 297 mm)',
                                            'a5' => 'A5 (Compact 148 × 210 mm)',
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

                                Grid::make(2)->schema([
                                    TextInput::make('certificate_number')
                                        ->label('Certificate No')
                                        ->default(fn () => 'CERT-VHE/'.date('Y').'/RU/'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT).'/'.str_pad((string) rand(1, 99), 3, '0', STR_PAD_LEFT))
                                        ->required()
                                        ->live(onBlur: true),

                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'draft' => 'Draft Record',
                                            'issued' => 'Officially Issued',
                                        ])
                                        ->default('draft')
                                        ->required()
                                        ->live(),
                                ]),
                            ]),

                        Section::make('Client & Installation Premises')
                            ->schema([
                                Select::make('contact_id')
                                    ->label('Customer / Contact')
                                    ->relationship('contact', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?int $state) {
                                        if ($state && $contact = Contact::find($state)) {
                                            $set('customer_name', strtoupper($contact->name));
                                            if (! empty($contact->address)) {
                                                $set('site_address', $contact->address);
                                            }
                                        }
                                    }),

                                TextInput::make('customer_name')
                                    ->label('Customer Name on Certificate')
                                    ->placeholder('e.g., MR. M A N A M Fernando')
                                    ->required()
                                    ->live(onBlur: true),

                                Textarea::make('site_address')
                                    ->label('Installation Site Address')
                                    ->rows(2)
                                    ->placeholder('e.g., 260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa')
                                    ->required()
                                    ->live(onBlur: true),
                            ]),

                        Section::make('System & Engineering Specifications')
                            ->schema([
                                TextInput::make('system_capacity')
                                    ->label('System Capacity & Configuration')
                                    ->default('05 kW+05kw Battery offgrid Solar System')
                                    ->required()
                                    ->live(onBlur: true),

                                Grid::make(2)->schema([
                                    DatePicker::make('commissioning_date')
                                        ->label('Commissioning Date')
                                        ->default(now())
                                        ->required()
                                        ->live(),

                                    Select::make('grid_connection')
                                        ->label('Grid Interconnection')
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

                                Grid::make(2)->schema([
                                    TextInput::make('warranty_period')
                                        ->label('Warranty Terms')
                                        ->default('30 Years (Panels) / 10 Years (Inverter+Battery)')
                                        ->required()
                                        ->live(onBlur: true),

                                    TextInput::make('system_status')
                                        ->label('System Status')
                                        ->default('Commissioned & Operational')
                                        ->required()
                                        ->live(onBlur: true),
                                ]),

                                TextInput::make('verification_status')
                                    ->label('Verification Standard')
                                    ->default('Engineering Safety Compliant')
                                    ->required()
                                    ->live(onBlur: true),

                                Textarea::make('compliance_note')
                                    ->label('Compliance & Testing Note')
                                    ->rows(2)
                                    ->default('Installed, tested, and commissioned in accordance with standard safety regulations.')
                                    ->required()
                                    ->live(onBlur: true),
                            ]),

                        Section::make('Signatories & Certification')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('lead_engineer_name')
                                        ->label('Lead Engineer Name')
                                        ->default('Lead Engineer')
                                        ->required()
                                        ->live(onBlur: true),

                                    TextInput::make('lead_engineer_title')
                                        ->label('Engineer Designation')
                                        ->default('Technical Operations')
                                        ->required()
                                        ->live(onBlur: true),

                                    TextInput::make('director_name')
                                        ->label('Director / Signatory')
                                        ->default('Managing Director')
                                        ->required()
                                        ->live(onBlur: true),

                                    TextInput::make('director_title')
                                        ->label('Director Title / Company')
                                        ->default('Vertex Home Solutions (Pvt) Ltd')
                                        ->required()
                                        ->live(onBlur: true),
                                ]),

                                DatePicker::make('signed_date')
                                    ->label('Signed Date')
                                    ->default(now())
                                    ->required()
                                    ->live(),
                            ]),
                    ])->columnSpan([
                        'default' => 12,
                        'xl' => 5,
                    ]),

                    // Right Column (7 columns on desktop): Real-time interactive Live Preview
                    Grid::make(1)->schema([
                        View::make('filament.app.resources.completion-certificates.live-preview'),
                    ])->columnSpan([
                        'default' => 12,
                        'xl' => 7,
                    ]),
                ]),
            ]);
    }
}
