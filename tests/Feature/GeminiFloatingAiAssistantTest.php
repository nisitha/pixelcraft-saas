<?php

namespace Tests\Feature;

use App\Livewire\FloatingAiAssistant;
use App\Models\Category;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Quotation;
use App\Models\SalesAgent;
use App\Models\User;
use App\Services\GeminiAssistantService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class GeminiFloatingAiAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'lanka-solar-solutions')->firstOrFail();

        $this->user = User::firstOrCreate(
            ['email' => 'ai_test_owner@lankasolar.lk'],
            ['name' => 'AI Test Owner', 'password' => Hash::make('Password@123'), 'is_active' => true]
        );

        if (! $this->company->users()->where('user_id', $this->user->id)->exists()) {
            $this->company->users()->attach($this->user->id, ['role' => 'owner']);
        }

        $this->actingAs($this->user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    public function test_floating_ai_assistant_mounts_and_renders_widget(): void
    {
        $component = Livewire::test(FloatingAiAssistant::class);

        $component->assertStatus(200);
        $component->assertSee('Pixel Craft Studios AI Assistant');

        // Test toggle open
        $this->assertFalse($component->get('isOpen'));
        $component->call('toggleOpen');
        $this->assertTrue($component->get('isOpen'));
        $component->assertSee('Pixel Craft Studios AI');
        $component->assertSee('Gemini 2.0 Flash');

        // Test clear chat
        $component->set('input', 'Hello');
        $component->call('clearChat');
        $this->assertEmpty($component->get('input'));
        $this->assertCount(1, $component->get('messages'));
    }

    public function test_gemini_service_project_profit_analysis_tool_scoping(): void
    {
        $tenantId = $this->company->id;

        $agent = SalesAgent::create([
            'company_id' => $tenantId,
            'name' => 'Agent Silva',
            'agent_code' => 'AGT-SILVA',
            'phone' => '0779998877',
        ]);

        $customer = Contact::create([
            'company_id' => $tenantId,
            'name' => 'Mr. W. Fernando',
            'phone' => '0771122334',
            'ceb_account_no' => 'CEB-FERNANDO-01',
        ]);

        $invoice = Invoice::create([
            'company_id' => $tenantId,
            'contact_id' => $customer->id,
            'sales_agent_id' => $agent->id,
            'invoice_number' => 'INV-FERNANDO-001',
            'invoice_date' => '2026-03-01',
            'grand_total' => 1460000.00,
            'paid_amount' => 1460000.00,
            'due_amount' => 0.00,
            'commission_amount' => 30000.00,
            'commission_status' => 'paid',
            'status' => 'paid',
        ]);

        // Direct material bill
        Expense::create([
            'company_id' => $tenantId,
            'invoice_id' => $invoice->id,
            'title' => 'Fortune Green Solar Panels Bill',
            'category' => 'Materials',
            'amount' => 1073299.00,
            'expense_date' => '2026-03-02',
            'is_internal_overhead' => false,
        ]);

        // Installation labor
        Expense::create([
            'company_id' => $tenantId,
            'invoice_id' => $invoice->id,
            'title' => 'Installation Technician Team',
            'category' => 'Installation Labour',
            'amount' => 35000.00,
            'expense_date' => '2026-03-03',
            'is_internal_overhead' => false,
        ]);

        // Internal office overhead absorption
        Expense::create([
            'company_id' => $tenantId,
            'invoice_id' => $invoice->id,
            'title' => 'Office Overhead Allocation',
            'category' => 'Office Overhead',
            'amount' => 112000.00,
            'expense_date' => '2026-03-04',
            'is_internal_overhead' => true,
        ]);

        $service = app(GeminiAssistantService::class);
        $result = $service->getProjectProfitAnalysis('Fernando', $tenantId);

        $this->assertTrue($result['found']);
        $this->assertEquals('Mr. W. Fernando', $result['customer_name']);
        $this->assertEquals(1460000.00, $result['contract_revenue_lkr']);
        $this->assertEquals(1073299.00, $result['itemized_material_bills_lkr']);
        $this->assertEquals(35000.00, $result['installation_labour_lkr']);
        $this->assertEquals(30000.00, $result['agent_commission_lkr']);
        $this->assertEquals(112000.00, $result['office_overhead_lkr']);
        $this->assertEquals(321701.00, $result['company_retained_margin_lkr']);
        $this->assertEquals(209701.00, $result['net_owner_profit_lkr']);
        $this->assertTrue($result['is_profitable']);

        // Verify other company cannot access this data
        $otherCompany = Company::create([
            'name' => 'Foreign Corp',
            'slug' => 'foreign-corp',
        ]);
        $otherResult = $service->getProjectProfitAnalysis('Fernando', $otherCompany->id);
        $this->assertFalse($otherResult['found']);
    }

    public function test_gemini_service_financial_kpi_summary_tool(): void
    {
        $tenantId = $this->company->id;

        $customer = Contact::create([
            'company_id' => $tenantId,
            'name' => 'KPI Test Customer',
            'phone' => '0710001122',
        ]);

        $invoice = Invoice::create([
            'company_id' => $tenantId,
            'contact_id' => $customer->id,
            'invoice_number' => 'INV-KPI-001',
            'invoice_date' => now()->toDateString(),
            'grand_total' => 500000.00,
            'subtotal' => 500000.00,
            'profit_markup' => 50000.00,
            'paid_amount' => 300000.00,
            'due_amount' => 200000.00,
            'commission_amount' => 15000.00,
            'status' => 'partial',
        ]);

        Payment::create([
            'company_id' => $tenantId,
            'invoice_id' => $invoice->id,
            'contact_id' => $customer->id,
            'payment_number' => 'PAY-KPI-001',
            'payment_date' => now()->toDateString(),
            'amount' => 300000.00,
            'payment_method' => 'bank_transfer',
        ]);

        $service = app(GeminiAssistantService::class);
        $kpi = $service->getFinancialKpiSummary('all_time', $tenantId);

        $this->assertGreaterThanOrEqual(500000.00, $kpi['total_invoiced_sales_lkr']);
        $this->assertGreaterThanOrEqual(300000.00, $kpi['cash_payments_collected_lkr']);
        $this->assertGreaterThanOrEqual(200000.00, $kpi['pending_cash_to_collect_lkr']);
        $this->assertArrayHasKey('net_profit_lkr', $kpi);
        $this->assertArrayHasKey('retained_margin_lkr', $kpi);
    }

    public function test_gemini_service_unpaid_invoices_tool(): void
    {
        $tenantId = $this->company->id;

        $customer = Contact::create([
            'company_id' => $tenantId,
            'name' => 'Debtor Customer',
            'phone' => '0775554433',
        ]);

        Invoice::create([
            'company_id' => $tenantId,
            'contact_id' => $customer->id,
            'invoice_number' => 'INV-UNPAID-999',
            'invoice_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(2)->toDateString(),
            'grand_total' => 250000.00,
            'paid_amount' => 50000.00,
            'due_amount' => 200000.00,
            'status' => 'partial',
        ]);

        $service = app(GeminiAssistantService::class);
        $result = $service->getUnpaidInvoices($tenantId);

        $this->assertGreaterThanOrEqual(1, $result['total_unpaid_count']);
        $this->assertGreaterThanOrEqual(200000.00, $result['total_balance_due_lkr']);

        $found = collect($result['unpaid_invoices'])->firstWhere('invoice_number', 'INV-UNPAID-999');
        $this->assertNotNull($found);
        $this->assertEquals('Debtor Customer', $found['customer_name']);
        $this->assertEquals(200000.00, $found['balance_due_lkr']);
        $this->assertEquals(2, $found['days_overdue']);
    }

    public function test_gemini_service_inventory_status_tool(): void
    {
        $tenantId = $this->company->id;

        $cat = Category::create([
            'company_id' => $tenantId,
            'name' => 'Inverters',
            'type' => 'product',
        ]);

        $prod = Product::create([
            'company_id' => $tenantId,
            'category_id' => $cat->id,
            'name' => 'Growatt 5kW Hybrid Inverter',
            'sku' => 'INV-GW-5K',
            'stock_qty' => 4,
            'unit_price' => 290000.00,
            'cost_price' => 240000.00,
            'track_serial' => true,
            'warranty_months' => 60,
        ]);

        ProductSerial::create([
            'company_id' => $tenantId,
            'product_id' => $prod->id,
            'serial_number' => 'GW5K-SN-998811',
            'status' => 'in_stock',
        ]);

        $service = app(GeminiAssistantService::class);
        $result = $service->getInventoryStatus('Inverter', $tenantId);

        $this->assertGreaterThanOrEqual(1, $result['product_count']);
        $found = collect($result['products'])->firstWhere('sku', 'INV-GW-5K');
        $this->assertNotNull($found);
        $this->assertEquals(4.0, $found['stock_qty']);
        $this->assertEquals(240000.00, $found['cost_price_lkr']);
        $this->assertContains('GW5K-SN-998811', $found['available_serials']);
    }

    public function test_gemini_service_customer_overview_tool(): void
    {
        $tenantId = $this->company->id;

        $customer = Contact::create([
            'company_id' => $tenantId,
            'name' => 'Mr. Gamini Perera',
            'phone' => '0778899001',
            'ceb_account_no' => 'CEB-GAMINI-77',
            'balance' => 0.00,
        ]);

        Quotation::create([
            'company_id' => $tenantId,
            'contact_id' => $customer->id,
            'quote_number' => 'Q-GAMINI-001',
            'system_kw' => 5.0,
            'grand_total' => 1600000.00,
            'status' => 'accepted',
            'quote_date' => '2026-03-01',
        ]);

        $service = app(GeminiAssistantService::class);
        $result = $service->getCustomerOverview('Gamini', $tenantId);

        $this->assertTrue($result['customer_found']);
        $this->assertEquals('Mr. Gamini Perera', $result['name']);
        $this->assertEquals('CEB-GAMINI-77', $result['ceb_account_no']);
        $this->assertCount(1, $result['projects']);
        $this->assertEquals('Q-GAMINI-001', $result['projects'][0]['quote_number']);
        $this->assertEquals(5.0, $result['projects'][0]['system_kw']);
    }

    public function test_gemini_native_function_calling_and_livewire_execution(): void
    {
        $tenantId = $this->company->id;

        // Mock Gemini HTTP endpoint
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => Http::sequence()
                // Step 1: Model responds with functionCall to get_project_profit_analysis
                ->push([
                    'candidates' => [
                        [
                            'content' => [
                                'role' => 'model',
                                'parts' => [
                                    [
                                        'functionCall' => [
                                            'name' => 'get_project_profit_analysis',
                                            'args' => [
                                                'search_query' => 'Fernando',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200)
                // Step 2: After receiving functionResponse, model responds with final formatted message
                ->push([
                    'candidates' => [
                        [
                            'content' => [
                                'role' => 'model',
                                'parts' => [
                                    [
                                        'text' => "### Project Profit Analysis for Mr. Fernando\n- **Contract Revenue:** LKR 1,460,000.00\n- **Material Bills:** LKR 1,073,299.00\n- **Installation Labour:** LKR 35,000.00\n- **Agent Commission:** LKR 30,000.00\n- **Internal Office Overhead:** LKR 112,000.00\n- **Net Owner Profit:** LKR 209,701.00 (14.4%)",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $component = Livewire::test(FloatingAiAssistant::class);
        $component->call('sendMessage', 'What is the profit breakdown for Fernando?');

        $messages = $component->get('messages');
        $this->assertCount(3, $messages); // Initial greeting + user query + assistant response
        $this->assertEquals('user', $messages[1]['role']);
        $this->assertEquals('What is the profit breakdown for Fernando?', $messages[1]['content']);
        $this->assertEquals('assistant', $messages[2]['role']);
        $this->assertStringContainsString('LKR 1,460,000.00', $messages[2]['content']);
        $this->assertStringContainsString('LKR 209,701.00', $messages[2]['content']);
    }

    public function test_render_hook_renders_floating_ai_assistant_in_app_panel(): void
    {
        $response = $this->get('/app/'.$this->company->slug);

        $response->assertStatus(200);
        $response->assertSee('Pixel Craft Studios AI Assistant');
    }

    public function test_gemini_preserves_thought_signature_and_raw_metadata_in_function_calling_loop(): void
    {
        $tenantId = $this->company->id;

        $sentPayloads = [];

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => function ($request) use (&$sentPayloads) {
                $payload = json_decode($request->body(), true);
                $sentPayloads[] = $payload;

                if (count($sentPayloads) === 1) {
                    return Http::response([
                        'candidates' => [
                            [
                                'content' => [
                                    'role' => 'model',
                                    'parts' => [
                                        [
                                            'thought' => 'Thinking: User is requesting unpaid invoices.',
                                            'thought_signature' => 'sig_abc123_xyz',
                                        ],
                                        [
                                            'functionCall' => [
                                                'name' => 'get_unpaid_invoices',
                                                'args' => (object) [],
                                            ],
                                            'thought_signature' => 'sig_call_456',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ], 200);
                }

                return Http::response([
                    'candidates' => [
                        [
                            'content' => [
                                'role' => 'model',
                                'parts' => [
                                    [
                                        'text' => 'You have 0 overdue invoices.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200);
            },
        ]);

        $service = app(GeminiAssistantService::class);
        $response = $service->chat([
            ['role' => 'user', 'content' => 'Show unpaid invoices'],
        ], $tenantId);

        $this->assertEquals('You have 0 overdue invoices.', $response);
        $this->assertCount(2, $sentPayloads);

        // Verify the second request sent back to Gemini preserved the exact candidate content with thought_signature
        $secondPayloadContents = $sentPayloads[1]['contents'];
        $modelTurn = $secondPayloadContents[1];
        $this->assertEquals('model', $modelTurn['role']);
        $this->assertEquals('sig_abc123_xyz', $modelTurn['parts'][0]['thought_signature'] ?? null);
        $this->assertEquals('sig_call_456', $modelTurn['parts'][1]['thought_signature'] ?? null);
        $this->assertEquals('Thinking: User is requesting unpaid invoices.', $modelTurn['parts'][0]['thought'] ?? null);

        // Verify the third turn is user functionResponse
        $userFunctionResponseTurn = $secondPayloadContents[2];
        $this->assertEquals('user', $userFunctionResponseTurn['role']);
        $this->assertEquals('get_unpaid_invoices', $userFunctionResponseTurn['parts'][0]['functionResponse']['name']);
    }

    public function test_timeout_is_handled_gracefully_in_service_and_livewire(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/*' => function () {
                throw new ConnectionException('cURL error 28: Operation timed out after 90001 milliseconds with 0 bytes received');
            },
        ]);

        $component = Livewire::test(FloatingAiAssistant::class);
        $component->call('sendMessage', 'Calculate total company profit');

        $this->assertFalse($component->get('isLoading'));
        $messages = $component->get('messages');
        $this->assertCount(3, $messages);
        $this->assertStringContainsString('Calculation took too long', $messages[2]['content']);
    }
}
