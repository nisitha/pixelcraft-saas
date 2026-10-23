<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CommissionPayout;
use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesAgent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GaminiProjectDataSeeder extends Seeder
{
    /**
     * Run the database seeds for Vertex Homes 10.2kW Solar Project for Mr. Gamini
     * Agreement / Quotation / Invoice: VHE/2026/00179 (Date: 2026-09-21)
     */
    public function run(): void
    {
        // 1. Resolve Vertex Homes Company
        $company = Company::where('slug', 'vertex-homes')->first()
            ?? Company::where('name', 'like', '%VERTEX HOME SOLUTIONS%')->first();

        if (! $company) {
            $this->call(VertexHomesSeeder::class);
            $company = Company::where('slug', 'vertex-homes')->firstOrFail();
        }

        // 2. Ensure Categories Exist
        $categoryMap = [
            'Inverters' => 'inverters',
            'Battery Energy Storage' => 'battery-energy-storage',
            'Solar PV Panels' => 'solar-pv-panels',
            'Mounting & Hardware' => 'mounting-hardware',
            'Electrical Switchgear & Protection' => 'electrical-switchgear-protection',
            'Cables & Wiring' => 'cables-wiring',
            'Installation & Engineering Services' => 'installation-engineering-services',
            'Logistics & Transport' => 'logistics-transport',
        ];

        $categories = [];
        foreach ($categoryMap as $name => $slug) {
            $categories[$name] = Category::updateOrCreate(
                ['company_id' => $company->id, 'slug' => $slug],
                ['name' => $name]
            );
        }

        // 3. Seed Suppliers
        $suppliersData = [
            'Shree Energy (Pvt) Ltd' => [
                'name' => 'Shree Energy (Pvt) Ltd',
                'address' => 'No 9, Vivian Gunawardhana Road, Wallampitiya',
                'phone' => '0760332537 / 0764548088',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Asian Electronics & Electrical' => [
                'name' => 'Asian Electronics & Electrical',
                'address' => 'Danowita Road, Mirigama',
                'phone' => '0724365237 / 0771298310',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Victorines' => [
                'name' => 'Victorines',
                'address' => 'No-100, Negombo Road, Ja-Ela',
                'phone' => '0716503737',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Ajantha Hardware' => [
                'name' => 'Ajantha Hardware',
                'address' => 'No. 48, Negombo Road, Mirigama',
                'phone' => '033 2273362',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Fortune Green (Pvt) Ltd' => [
                'name' => 'Fortune Green (Pvt) Ltd',
                'address' => 'No. 120, Biyagama Road, Kelaniya, Sri Lanka',
                'phone' => '+94 11 234 5678',
                'email' => 'sales@fortunegreen.lk',
                'tax_no' => 'TIN-40918234-001',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Vertex Store' => [
                'name' => 'Vertex Store',
                'address' => 'No. 33/A, Hapugahagedara, Loluwagoda, Mirigama',
                'phone' => '+94 70 2 271 271',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Anthony Hardware' => [
                'name' => 'Anthony Hardware',
                'address' => 'Mirigama',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Electrical Supplier' => [
                'name' => 'Electrical Supplier',
                'address' => 'Colombo / Mirigama',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
            'Sundry Site Materials' => [
                'name' => 'Sundry Site Materials',
                'address' => 'Mirigama Site',
                'type' => 'supplier',
                'balance' => 0.00,
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $name => $data) {
            $suppliers[$name] = Contact::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => $name,
                ],
                $data
            );
        }

        // 4. Seed Products with Exact Unit & Cost Prices
        $productsData = [
            [
                'name' => 'Sumry 10.2kW Off-grid Inverter',
                'sku' => 'INV-SUMRY-10.2KW-OFFGRID',
                'category_id' => $categories['Inverters']->id,
                'type' => 'product',
                'unit_price' => 260000.00,
                'cost_price' => 225000.00, // Shree Energy 202604146
                'stock_qty' => 10.00,
                'alert_qty' => 2.00,
                'track_serial' => true,
                'warranty_months' => 60, // 5 Years Warranty
            ],
            [
                'name' => 'LiFePO4 Lithium Battery 51.2V 314Ah Powerwall (16kWh)',
                'sku' => 'BAT-LIFEPO4-51.2V-314AH',
                'category_id' => $categories['Battery Energy Storage']->id,
                'type' => 'product',
                'unit_price' => 850000.00,
                'cost_price' => 680000.00, // Fortune Green VH-FG-20263606
                'stock_qty' => 6.00,
                'alert_qty' => 1.00,
                'track_serial' => true,
                'warranty_months' => 120, // 10 Years Warranty
            ],
            [
                'name' => '8-Way Weatherproof Box IP65',
                'sku' => 'SW-8WAY-BOX-IP65',
                'category_id' => $categories['Electrical Switchgear & Protection']->id,
                'type' => 'product',
                'unit_price' => 3500.00,
                'cost_price' => 2900.00, // Asian Electronics ASIAN-220926 (2x @ 2,900 = 5,800.00)
                'stock_qty' => 25.00,
                'alert_qty' => 4.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
            [
                'name' => 'Jinko 640W PV Panel',
                'sku' => 'PANEL-JINKO-640W-BIFACIAL',
                'category_id' => $categories['Solar PV Panels']->id,
                'type' => 'product',
                'unit_price' => 47500.00,
                'cost_price' => 45021.25, // 10 x 45,021.25 = 450,212.50 (Vertex Store CASH-SALE-004)
                'stock_qty' => 80.00,
                'alert_qty' => 10.00,
                'track_serial' => true,
                'warranty_months' => 360, // 30 Years Warranty
            ],
            [
                'name' => 'Surge Protection & Mounting Structure Set',
                'sku' => 'MNT-BOS-10.2KW-SET',
                'category_id' => $categories['Mounting & Hardware']->id,
                'type' => 'product',
                'unit_price' => 160000.00,
                'cost_price' => 131380.00, // Anthony (66,160) + Beny (38,200) + Victorines (17,420) + Asian (5,800) + Ajantha (1,800) + Sundry (2,000)
                'stock_qty' => 15.00,
                'alert_qty' => 2.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'name' => 'Transport & Turnkey Installation',
                'sku' => 'SRV-TURNKEY-INSTALL-10.2KW',
                'category_id' => $categories['Installation & Engineering Services']->id,
                'type' => 'service',
                'unit_price' => 180000.00,
                'cost_price' => 197000.00, // Labor (85,000.00) + Office Overhead Allocation (112,000.00)
                'stock_qty' => 999.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
        ];

        $products = [];
        foreach ($productsData as $data) {
            $products[$data['sku']] = Product::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'sku' => $data['sku'],
                ],
                $data
            );
        }

        // 5. Seed / Update Sales Agent S L R Krishantha (3% Commission)
        $agentKrishantha = SalesAgent::where('company_id', $company->id)
            ->where(function ($q) {
                $q->where('name', 'S L R Krishantha')
                    ->orWhere('agent_code', 'AGT-003');
            })
            ->first();

        if ($agentKrishantha) {
            $agentKrishantha->update([
                'name' => 'S L R Krishantha',
                'bank_name' => "People's Bank",
                'bank_branch' => 'Mirigama Branch',
                'bank_account_name' => 'S L R Krishantha',
                'bank_account_no' => '269200168492252',
                'default_commission_type' => 'percentage',
                'default_commission_value' => 3.00,
                'is_active' => true,
            ]);
        } else {
            $agentKrishantha = SalesAgent::create([
                'company_id' => $company->id,
                'agent_code' => 'AGT-003',
                'name' => 'S L R Krishantha',
                'nic_number' => '198512948210',
                'phone' => '077 889 9112',
                'email' => 'krishantha.solar@example.com',
                'address' => 'No. 54/2, Negombo Road, Katana',
                'bank_name' => "People's Bank",
                'bank_branch' => 'Mirigama Branch',
                'bank_account_name' => 'S L R Krishantha',
                'bank_account_no' => '269200168492252',
                'default_commission_type' => 'percentage',
                'default_commission_value' => 3.00,
                'is_active' => true,
                'notes' => 'Sales consultant for Gamini 10.2kW Project (3% project commission)',
            ]);
        }

        // 6. Ensure Customer Mr. Gamini Exists
        $customerGamini = Contact::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Mr. Gamini',
            ],
            [
                'type' => 'customer',
                'phone' => '+94 77 123 4567',
                'address' => 'Danowita Road, Mirigama',
                'balance' => 0.00,
            ]
        );

        // 7. Seed Quotation VHE/2026/00179
        $quotation = Quotation::updateOrCreate(
            [
                'company_id' => $company->id,
                'quote_number' => 'VHE/2026/00179',
            ],
            [
                'contact_id' => $customerGamini->id,
                'sales_agent_id' => $agentKrishantha->id,
                'quote_date' => '2026-09-21',
                'valid_until' => '2026-10-05',
                'status' => 'accepted',
                'system_kw' => 10.20,
                'phase_type' => 'single',
                'roof_type' => 'Tile / Asbestos',
                'panel_brand' => 'Jinko 640W',
                'inverter_brand' => '10.2kW Sumry Off-grid',
                'price_display_mode' => 'package_total_only',
                'pdf_item_summary_mode' => 'consolidated',
                'subtotal' => 1925000.00,
                'discount' => 25000.00,
                'tax' => 0.00,
                'grand_total' => 1900000.00,
                'commission_amount' => 57000.00,
                'commission_status' => 'paid',
                'terms' => "1. Valid for 14 days from date of issue.\n2. Milestone 1: 50% Advance upon contract signing (LKR 950,000.00).\n3. Milestone 2: 25% Second payment on delivery of equipment (LKR 475,000.00).\n4. Milestone 3: 25% Final payment upon installation & commissioning (LKR 475,000.00).\n5. Warranty: 30 Years on Jinko Panels, 5 Years on Sumry Inverter, 10 Years on Lithium Battery, 2 Years comprehensive workmanship warranty.",
                'is_sent' => true,
                'sent_at' => '2026-09-21 09:00:00',
            ]
        );

        // 8. Seed Invoice VHE/2026/00179
        $invoice = Invoice::updateOrCreate(
            [
                'company_id' => $company->id,
                'invoice_number' => 'VHE/2026/00179',
            ],
            [
                'quotation_id' => $quotation->id,
                'contact_id' => $customerGamini->id,
                'sales_agent_id' => $agentKrishantha->id,
                'invoice_date' => '2026-09-21',
                'due_date' => '2026-09-26',
                'type' => 'milestone',
                'price_display_mode' => 'package_total_only',
                'pdf_item_summary_mode' => 'consolidated',
                'subtotal' => 1925000.00,
                'discount' => 25000.00,
                'tax' => 0.00,
                'grand_total' => 1900000.00,
                'paid_amount' => 1900000.00,
                'due_amount' => 0.00,
                'commission_amount' => 57000.00, // 3% of LKR 1,900,000.00
                'commission_status' => 'paid',
                'status' => 'paid',
                'is_sent' => true,
                'sent_at' => '2026-09-21 09:30:00',
            ]
        );

        // 9. Populate the 5 Customer Items
        // Total Subtotal = LKR 1,925,000.00, Discount = LKR 25,000.00 -> Grand Total = LKR 1,900,000.00
        // Items Total Cost = LKR 1,683,592.50
        // Gross Profit = LKR 216,407.50
        // Agent Commission = LKR 57,000.00
        // Total Project Cost = LKR 1,740,592.50
        // Net Owner Profit = LKR 159,407.50 (8.39% margin)
        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        QuotationItem::where('quotation_id', $quotation->id)->delete();

        $items = [
            [
                'product_id' => $products['PANEL-JINKO-640W-BIFACIAL']->id,
                'description' => 'Jinko 640W PV Panels (10x 640W Tier-1 Modules - 6.40 kWp DC Array)',
                'warranty_text' => '30 Years Performance Warranty',
                'quantity' => 10,
                'unit_price' => 47500.00,
                'cost_price' => 45021.25, // 10 x 45,021.25 = 450,212.50 (CASH-SALE-004)
                'subtotal' => 475000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['INV-SUMRY-10.2KW-OFFGRID']->id,
                'description' => 'Sumry 10.2kW Pure Sine Wave Off-Grid Solar Inverter (48V DC, Dual MPPT 160A)',
                'warranty_text' => '5 Years Manufacturer Warranty',
                'quantity' => 1,
                'unit_price' => 260000.00,
                'cost_price' => 225000.00, // Shree Energy 202604146
                'subtotal' => 260000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['BAT-LIFEPO4-51.2V-314AH']->id,
                'description' => 'LiFePO4 Lithium Battery 51.2V 314Ah Powerwall Energy Storage Pack (16.07 kWh Storage)',
                'warranty_text' => '10 Years Manufacturer Warranty',
                'quantity' => 1,
                'unit_price' => 850000.00,
                'cost_price' => 680000.00, // Fortune Green VH-FG-20263606
                'subtotal' => 850000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['MNT-BOS-10.2KW-SET']->id,
                'description' => 'Surge Protection & Mounting Structure Set (Beny SPDs, MCBs, ATS, 2x IP65 DBs, Cables & Railings)',
                'warranty_text' => '5 Years Warranty',
                'quantity' => 1,
                'unit_price' => 160000.00,
                'cost_price' => 131380.00, // Anthony (66,160) + Beny (38,200) + Victorines (17,420) + Asian (5,800) + Ajantha (1,800) + Sundry (2,000)
                'subtotal' => 160000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SRV-TURNKEY-INSTALL-10.2KW']->id,
                'description' => 'Turnkey Engineering Installation, Commissioning & Project Overhead Allocation',
                'warranty_text' => '2 Years Comprehensive Workmanship Warranty',
                'quantity' => 1,
                'unit_price' => 180000.00,
                'cost_price' => 197000.00, // 85,000.00 (Installing charges) + 112,000.00 (Office overhead allocation)
                'subtotal' => 180000.00,
                'tax' => 0.00,
            ],
        ];

        foreach ($items as $item) {
            InvoiceItem::create(array_merge($item, ['invoice_id' => $invoice->id]));
            QuotationItem::create(array_merge($item, ['quotation_id' => $quotation->id]));
        }

        // 10. Register Inverter Serial Number under ProductSerial with 5 Years Warranty
        ProductSerial::updateOrCreate(
            [
                'company_id' => $company->id,
                'serial_number' => 'SN 9201232882604140992',
            ],
            [
                'product_id' => $products['INV-SUMRY-10.2KW-OFFGRID']->id,
                'invoice_id' => $invoice->id,
                'contact_id' => $customerGamini->id,
                'warranty_months' => 60,
                'warranty_expires_at' => Carbon::parse('2026-09-21')->addMonths(60)->toDateString(),
                'status' => 'sold',
                'notes' => '5-Year Manufacturer Warranty on Sumry 10.2kW Off-grid Inverter for Mr. Gamini (VHE/2026/00179)',
            ]
        );

        // 11. Record the 3 Actual Customer Payments against this invoice (Total: LKR 1,900,000.00)
        Payment::where('invoice_id', $invoice->id)->delete();

        // Payment 1: 2026-09-21 | LKR 900,000.00 (Advance)
        Payment::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'payment_date' => '2026-09-21',
            'amount' => 900000.00,
            'method' => 'bank_transfer',
            'milestone_label' => '50% Advance Payment (Part 1 - Contract Signing)',
            'reference_no' => 'TXN-DFCC-2026-0921',
            'notes' => 'Advance Payment for 10.2kW Solar Project (VHE/2026/00179) - Mr. Gamini',
        ]);

        // Payment 2: 2026-09-23 | LKR 525,000.00 (Second Payment)
        Payment::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'payment_date' => '2026-09-23',
            'amount' => 525000.00,
            'method' => 'bank_transfer',
            'milestone_label' => 'Second Payment (Advance Balance & Equipment Delivery)',
            'reference_no' => 'TXN-BOC-2026-0923',
            'notes' => 'Second installment for 10.2kW Solar Project (VHE/2026/00179) - Mr. Gamini',
        ]);

        // Payment 3: 2026-09-26 | LKR 475,000.00 (Final Payment)
        Payment::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'payment_date' => '2026-09-26',
            'amount' => 475000.00,
            'method' => 'bank_transfer',
            'milestone_label' => '25% Final Payment (Testing, Commissioning & Handover)',
            'reference_no' => 'TXN-BOC-2026-0926',
            'notes' => 'Final payment upon handover of 10.2kW Solar System (VHE/2026/00179) - Mr. Gamini',
        ]);

        // 12. Create Commission Payout Voucher for S L R Krishantha (LKR 57,000.00)
        CommissionPayout::updateOrCreate(
            [
                'company_id' => $company->id,
                'voucher_number' => 'COM-VHE-2026-0003',
            ],
            [
                'sales_agent_id' => $agentKrishantha->id,
                'invoice_id' => $invoice->id,
                'payout_date' => '2026-09-26',
                'amount' => 57000.00,
                'payment_method' => 'bank_transfer',
                'reference_no' => 'COMM-KRISHANTHA',
                'status' => 'paid',
                'notes' => '3% Sales Commission settlement for Mr. Gamini 10.2kW Off-Grid Solar Project (VHE/2026/00179)',
                'paper_size' => 'a5',
                'orientation' => 'landscape',
            ]
        );

        // 13. Seed all 9 Linked Project Expenses (Materials LKR 1,486,592.50 + Operational LKR 254,000.00 = Total Cost LKR 1,740,592.50)
        Expense::where('invoice_id', $invoice->id)->delete();
        $refNumbers = [
            'VH-FG-20263606', '202604146', 'CASH-SALE-004', 'CASH-SALE-005',
            'CASH-SALE-006', '7951497', 'ASIAN-220926', 'AJANTHA-220926',
            'SITE-SUNDRY', 'LABOUR-GAMINI', 'OFFICE-EXP-GAMINI', 'COMM-KRISHANTHA',
        ];
        Expense::where('company_id', $company->id)
            ->whereIn('reference_no', $refNumbers)
            ->delete();

        $projectExpenses = [
            // Materials Bills (9 items - LKR 1,486,592.50)
            [
                'supplier_id' => $suppliers['Fortune Green (Pvt) Ltd']->id,
                'category' => 'Hardware & Tools',
                'title' => 'LiFePO4 51.2V 314Ah Battery',
                'amount' => 680000.00,
                'expense_date' => '2026-10-09',
                'reference_no' => 'VH-FG-20263606',
                'notes' => 'LiFePO4 Lithium Battery 51.2V 314Ah Powerwall (16kWh) for Mr. Gamini',
            ],
            [
                'supplier_id' => $suppliers['Shree Energy (Pvt) Ltd']->id,
                'category' => 'Hardware & Tools',
                'title' => 'Sumry 10.2kW Inverter',
                'amount' => 225000.00,
                'expense_date' => '2026-09-21',
                'reference_no' => '202604146',
                'notes' => 'Sumry 10.2kW Off-grid Solar Inverter (SN 9201232882604140992)',
            ],
            [
                'supplier_id' => $suppliers['Vertex Store']->id,
                'category' => 'Hardware & Tools',
                'title' => '10x Jinko 640W Panels + BOS Accessories',
                'amount' => 450212.50,
                'expense_date' => '2026-09-20',
                'reference_no' => 'CASH-SALE-004',
                'notes' => '10x Jinko 640W Solar PV Panels & balance of system accessories',
            ],
            [
                'supplier_id' => $suppliers['Anthony Hardware']->id,
                'category' => 'Hardware & Tools',
                'title' => 'DC/AC Cables, DB Boxes, Ties',
                'amount' => 66160.00,
                'expense_date' => '2026-09-20',
                'reference_no' => 'CASH-SALE-005',
                'notes' => 'DC and AC cables, distribution boxes, cable ties',
            ],
            [
                'supplier_id' => $suppliers['Electrical Supplier']->id,
                'category' => 'Hardware & Tools',
                'title' => 'Beny DC/AC SPDs, MCBs & ATS',
                'amount' => 38200.00,
                'expense_date' => '2026-09-20',
                'reference_no' => 'CASH-SALE-006',
                'notes' => 'Beny DC SPDs, AC SPDs, DC/AC MCBs and Automatic Transfer Switch',
            ],
            [
                'supplier_id' => $suppliers['Victorines']->id,
                'category' => 'Hardware & Tools',
                'title' => 'Flexible pipes, 25*SGG Cable, Lugs',
                'amount' => 17420.00,
                'expense_date' => '2026-10-09',
                'reference_no' => '7951497',
                'notes' => 'Flexible conduit pipes, 25*SGG battery connection cable, and copper lugs',
            ],
            [
                'supplier_id' => $suppliers['Asian Electronics & Electrical']->id,
                'category' => 'Hardware & Tools',
                'title' => '2x 8-way Box IP65',
                'amount' => 5800.00,
                'expense_date' => '2026-09-22',
                'reference_no' => 'ASIAN-220926',
                'notes' => '2 units of 8-Way Weatherproof Box IP65 (Cost: Rs. 2,900 x 2)',
            ],
            [
                'supplier_id' => $suppliers['Ajantha Hardware']->id,
                'category' => 'Hardware & Tools',
                'title' => 'Hardware accessories',
                'amount' => 1800.00,
                'expense_date' => '2026-09-22',
                'reference_no' => 'AJANTHA-220926',
                'notes' => 'Hardware accessories, screws, plugs and brackets',
            ],
            [
                'supplier_id' => $suppliers['Sundry Site Materials']->id,
                'category' => 'Hardware & Tools',
                'title' => 'Silicon Gum, Tray Clips, Earth Wire',
                'amount' => 2000.00,
                'expense_date' => '2026-09-21',
                'reference_no' => 'SITE-SUNDRY',
                'notes' => 'Silicon gum, tray clips, and earth wire for installation',
            ],

            // Operational Expenses (3 items - LKR 254,000.00)
            [
                'supplier_id' => null,
                'category' => 'Labor & Contractor',
                'title' => 'Installing Charges (Panels 75k + Battery 10k)',
                'amount' => 85000.00,
                'expense_date' => '2026-09-26',
                'reference_no' => 'LABOUR-GAMINI',
                'notes' => 'Turnkey solar panel mounting charges (Rs. 75,000) + battery system install (Rs. 10,000)',
            ],
            [
                'supplier_id' => null,
                'category' => 'Operations',
                'title' => 'Office Expenses Allocation',
                'amount' => 112000.00,
                'is_internal_overhead' => true,
                'expense_date' => '2026-09-26',
                'reference_no' => 'OFFICE-EXP-GAMINI',
                'notes' => 'Project design, site coordination, transport logistics and office overhead allocation',
            ],
            [
                'supplier_id' => null,
                'category' => 'Other',
                'title' => '3% Sales Commission - S L R Krishantha',
                'amount' => 57000.00,
                'expense_date' => '2026-09-26',
                'reference_no' => 'COMM-KRISHANTHA',
                'notes' => '3% Commission on LKR 1,900,000.00 paid to S L R Krishantha (People\'s Bank Acc: 269200168492252)',
            ],
        ];

        foreach ($projectExpenses as $data) {
            Expense::create(array_merge($data, [
                'company_id' => $company->id,
                'invoice_id' => $invoice->id,
            ]));
        }

        // 14. Reconcile / Seed Completion Certificate
        CompletionCertificate::updateOrCreate(
            [
                'company_id' => $company->id,
                'certificate_number' => 'CERT-VHE/2026/00179',
            ],
            [
                'contact_id' => $customerGamini->id,
                'invoice_id' => $invoice->id,
                'quotation_id' => $quotation->id,
                'customer_name' => 'Mr. Gamini',
                'system_capacity' => '10.20 kW Solar PV + 16kWh Lithium Battery Off-Grid System',
                'site_address' => 'Danowita Road, Mirigama',
                'commissioning_date' => '2026-09-26',
                'grid_connection' => 'Non',
                'warranty_period' => '30 Years (Panels) / 5 Years (Inverter) / 10 Years (Battery)',
                'system_status' => 'Commissioned & Operational',
                'verification_status' => 'Engineering Safety Compliant',
                'compliance_note' => 'Installed, tested, and commissioned in accordance with standard safety regulations.',
                'lead_engineer_name' => 'Lead Engineer',
                'lead_engineer_title' => 'Technical Operations',
                'director_name' => 'Managing Director',
                'director_title' => 'VERTEX HOME SOLUTIONS (PVT) LTD',
                'signed_date' => '2026-09-26',
                'paper_size' => 'a4',
                'orientation' => 'landscape',
                'status' => 'issued',
            ]
        );
    }
}
