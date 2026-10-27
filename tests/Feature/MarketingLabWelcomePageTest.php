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

    public function test_welcome_page_contains_framer_marketinglab_dark_design_tokens_and_fonts(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Urbanist');
        $response->assertSee('Inter');
        $response->assertSee('PIXELCRAFT');
        $response->assertSee('#070709');
        $response->assertSee('glass-card', false);
        $response->assertSee('ambient-mesh', false);
    }

    public function test_welcome_page_loads_framer_motion_engine(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('https://cdn.jsdelivr.net/npm/motion@12.4.2/+esm', false);
        $response->assertSee('data-motion-hero', false);
        $response->assertSee('inView', false);
        $response->assertSee('stagger', false);
    }

    public function test_welcome_page_contains_four_process_cards(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Our Process');
        $response->assertSee('01');
        $response->assertSee('Onboard & Brand', false);
        $response->assertSee('02');
        $response->assertSee('Engineer & Quote', false);
        $response->assertSee('03');
        $response->assertSee('Bill & Transact', false);
        $response->assertSee('04');
        $response->assertSee('Settle & Profit', false);
    }

    public function test_welcome_page_contains_features_and_specialized_solutions(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Platform Features');
        $response->assertSee('Solar PV Sizing', false);
        $response->assertSee('Zoho-Style Milestone Billing', false);
        $response->assertSee('Sub-Second Counter POS', false);
        $response->assertSee('EPF & ETF Payroll Engine', false);
        $response->assertSee('hero-dashboard.webp', false);
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
        $response->assertSee('billingSwitcher', false);
        $response->assertSee('switchBilling', false);
    }

    public function test_welcome_page_contains_faq_accordion_and_official_metadata(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('How does the 14-day free trial work?');
        $response->assertSee('Is the Solar PV sizing tool compliant with CEB & LECO?', false);
        $response->assertSee('support@pixelcraftlk.com');
        $response->assertSee('app.pixelcraftlk.com');
        $response->assertDontSee('pixelcraftstudioslk@gmail.com');
        $response->assertSee('/app/register');
        $response->assertSee('/app/login');
    }
}
