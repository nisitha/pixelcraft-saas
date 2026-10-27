<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingLabWelcomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_welcome_page_contains_framer_growthlab_editorial_tokens_and_fonts(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Anton');
        $response->assertSee('Plus Jakarta Sans');
        $response->assertSee('PIXELCRAFT');
        $response->assertSee('#FF3838');
        $response->assertSee('#F5F4F0');
        $response->assertSee('#0A0A0A');
    }

    public function test_welcome_page_loads_alpine_and_lucide(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('alpinejs', false);
        $response->assertSee('lucide', false);
        $response->assertSee('x-data', false);
    }

    public function test_welcome_page_contains_four_process_cards(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('OUR PROCESS');
        $response->assertSee('01');
        $response->assertSee('ONBOARD & BRAND', false);
        $response->assertSee('02');
        $response->assertSee('ENGINEER & QUOTE', false);
        $response->assertSee('03');
        $response->assertSee('BILL & TRANSACT', false);
        $response->assertSee('04');
        $response->assertSee('SETTLE & PROFIT', false);
    }

    public function test_welcome_page_contains_services_and_case_studies(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SERVICES');
        $response->assertSee('SOLAR PV SIZING', false);
        $response->assertSee('ZOHO-STYLE MILESTONE INVOICING', false);
        $response->assertSee('80MM COUNTER THERMAL POS', false);
        $response->assertSee('EPF & ETF STATUTORY PAYROLL', false);
        $response->assertSee('SELECTED CASE STUDIES', false);
    }

    public function test_welcome_page_renders_lkr_pricing_plans_and_billing_switcher(): void
    {
        Plan::create([
            'name' => 'Custom Growth Tier',
            'slug' => 'growth',
            'price_monthly' => 5000.00,
            'price_yearly' => 50000.00,
            'max_users' => 4,
            'max_invoices' => 200,
            'features' => ['Feature Alpha', 'Feature Beta'],
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Custom Growth Tier');
        $response->assertSee('Rs. 5,000');
        $response->assertSee('billing', false);
        $response->assertSee('Monthly Billing', false);
        $response->assertSee('Annual Billing', false);
    }

    public function test_welcome_page_contains_faq_accordion_and_official_metadata(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('FREQUENTLY ASKED QUESTIONS');
        $response->assertSee('How does the 14-day free trial work?');
        $response->assertSee('Is the Solar PV sizing tool compliant with CEB & LECO?', false);
        $response->assertSee('support@pixelcraftlk.com');
        $response->assertSee('app.pixelcraftlk.com');
        $response->assertDontSee('pixelcraftstudioslk@gmail.com');
        $response->assertSee('/app/register');
        $response->assertSee('/app/login');
    }
}
