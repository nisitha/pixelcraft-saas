<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Tenancy\EditCompanyProfile;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceBrandingPreviewerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('public');
    }

    public function test_branding_settings_page_renders_with_split_layout_and_live_preview(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        Livewire::test(EditCompanyProfile::class)
            ->assertSuccessful()
            ->assertSee('Live Interactive Document Preview')
            ->assertSee('branding-split-container')
            ->assertSee('a4-mockup-paper')
            ->assertSee('One-Click Designer Themes')
            ->assertSee('Tier 1 550W Mono PERC Half-Cell Solar Panels')
            ->assertSee('Huawei SUN2000-10KTL-M1 10kW On-Grid Inverter');
    }

    public function test_branding_form_updates_and_persists_brand_colors_and_prefixes(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $logo = UploadedFile::fake()->image('brand-logo.png', 400, 200);

        Livewire::test(EditCompanyProfile::class)
            ->fillForm([
                'name' => 'Apex Solar Technologies (Pvt) Ltd',
                'logo_path' => [$logo],
                'settings' => [
                    'primary_color' => '#2563eb',
                    'table_header_color' => '#1e3a8a',
                    'text_color' => '#ffffff',
                    'quote_prefix' => 'ASP/2026/',
                    'invoice_prefix' => 'ASP-INV-',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $company->refresh();
        $this->assertEquals('Apex Solar Technologies (Pvt) Ltd', $company->name);
        $this->assertNotNull($company->logo_path);
        $this->assertEquals($company->logo_path, $company->settings['logo_path']);
        $this->assertEquals('#2563eb', $company->settings['primary_color']);
        $this->assertEquals('#1e3a8a', $company->settings['table_header_color']);
        $this->assertEquals('#ffffff', $company->settings['text_color']);
        $this->assertEquals('ASP/2026/', $company->settings['quote_prefix']);
        $this->assertEquals('ASP-INV-', $company->settings['invoice_prefix']);
    }

    public function test_pdf_templates_render_with_custom_branding_colors(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $company->update([
            'settings' => array_merge($company->settings ?? [], [
                'primary_color' => '#7c3aed',
                'table_header_color' => '#4c1d95',
                'text_color' => '#f8fafc',
            ]),
        ]);

        $invoice = Invoice::first();
        $htmlInvoice = view('pdf.invoice', [
            'invoice' => $invoice,
            'company' => $company,
        ])->render();

        $this->assertStringContainsString('#7c3aed', $htmlInvoice);
        $this->assertStringContainsString('#4c1d95', $htmlInvoice);
        $this->assertStringContainsString('#f8fafc', $htmlInvoice);

        $quotation = Quotation::first();
        $htmlQuotation = view('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $company,
        ])->render();

        $this->assertStringContainsString('#7c3aed', $htmlQuotation);
        $this->assertStringContainsString('#4c1d95', $htmlQuotation);
        $this->assertStringContainsString('#f8fafc', $htmlQuotation);
    }
}
