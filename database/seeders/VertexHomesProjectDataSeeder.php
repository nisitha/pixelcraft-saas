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
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesAgent;
use Illuminate\Database\Seeder;

class VertexHomesProjectDataSeeder extends Seeder
{
    /**
     * Run the database seeds for Vertex Homes Katana Road 5kW Off-Grid Project
     * Customer: Mr. M.A.N.A.M. Fernando (Quotation/Agreement: VHE/2026/RU/00199/005).
     */
    public function run(): void
    {
        // 1. Resolve Vertex Homes Company
        $company = Company::where('slug', 'vertex-homes')->first()
            ?? Company::firstOrFail();

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
        // Supplier 1: Fortune Green (Pvt) Ltd
        $supplierFortuneGreen = Contact::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Fortune Green (Pvt) Ltd',
            ],
            [
                'type' => 'supplier',
                'phone' => '+94 11 234 5678',
                'email' => 'sales@fortunegreen.lk',
                'address' => 'No. 120, Biyagama Road, Kelaniya, Sri Lanka',
                'tax_no' => 'TIN-40918234-001',
                'balance' => 0.00,
            ]
        );

        // Supplier 2: Geyli Hardware
        $supplierGeyli = Contact::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Geyli Hardware',
            ],
            [
                'type' => 'supplier',
                'phone' => '+94 33 224 8899',
                'email' => 'geyli.hardware@example.com',
                'address' => 'No. 82, Melwaththa, Loluwagoda, Mirigama',
                'tax_no' => 'BR-GH-82910',
                'balance' => 0.00,
            ]
        );

        // Supplier 3: New Sampath Electricals
        $supplierSampath = Contact::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'New Sampath Electricals',
            ],
            [
                'type' => 'supplier',
                'phone' => '+94 33 227 4100',
                'address' => 'Mirigama Road, Giriulla',
                'tax_no' => 'BR-NSE-4412',
                'balance' => 0.00,
            ]
        );

        // 4. Seed Products with Exact Unit & Cost Prices
        $productsData = [
            [
                'name' => 'AE 630W Bifacial Panels',
                'sku' => 'PANEL-AE-630W-BIFACIAL',
                'category_id' => $categories['Solar PV Panels']->id,
                'type' => 'product',
                'unit_price' => 38000.00,
                'cost_price' => 29237.29, // 8 x 29,237.29 = 233,898.32 (Fortune Green VH-FG-20263610)
                'stock_qty' => 120.00,
                'alert_qty' => 16.00,
                'track_serial' => true,
                'warranty_months' => 360, // 30 Years Warranty
            ],
            [
                'name' => 'LiFePO4 51.2V 100Ah Battery',
                'sku' => 'BAT-LIFEPO4-51.2V-100AH',
                'category_id' => $categories['Battery Energy Storage']->id,
                'type' => 'product',
                'unit_price' => 385000.00,
                'cost_price' => 296610.17, // Fortune Green VH-FG-20263633
                'stock_qty' => 10.00,
                'alert_qty' => 2.00,
                'track_serial' => true,
                'warranty_months' => 120, // 10 Years Warranty
            ],
            [
                'name' => '5kW Off-Grid Solar Inverter',
                'sku' => 'INV-OFFGRID-5KW-48V',
                'category_id' => $categories['Inverters']->id,
                'type' => 'product',
                'unit_price' => 195000.00,
                'cost_price' => 146489.00,
                'stock_qty' => 12.00,
                'alert_qty' => 3.00,
                'track_serial' => true,
                'warranty_months' => 120, // 10 Years Warranty
            ],
            [
                'name' => 'D Rail 04',
                'sku' => 'MNT-D-RAIL-04',
                'category_id' => $categories['Mounting & Hardware']->id,
                'type' => 'product',
                'unit_price' => 8500.00,
                'cost_price' => 5762.72, // 4 x 5,762.72 = 23,050.88 (Fortune Green VH-FG-20263610)
                'stock_qty' => 80.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 120,
            ],
            [
                'name' => 'DC Circuit Breaker BB2-40',
                'sku' => 'SW-DC-BREAKER-BB2-40',
                'category_id' => $categories['Electrical Switchgear & Protection']->id,
                'type' => 'product',
                'unit_price' => 6800.00,
                'cost_price' => 4788.14, // 2 x 4,788.14 = 9,576.28 (Fortune Green VH-FG-20263610)
                'stock_qty' => 50.00,
                'alert_qty' => 6.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'name' => 'DC SPD BUD-40/2R',
                'sku' => 'SW-DC-SPD-BUD-40-2R',
                'category_id' => $categories['Electrical Switchgear & Protection']->id,
                'type' => 'product',
                'unit_price' => 7500.00,
                'cost_price' => 5254.24, // 2 x 5,254.24 = 10,508.48 (Fortune Green VH-FG-20263610)
                'stock_qty' => 40.00,
                'alert_qty' => 6.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'name' => 'AC SPD BUA-40/2',
                'sku' => 'SW-AC-SPD-BUA-40-2',
                'category_id' => $categories['Electrical Switchgear & Protection']->id,
                'type' => 'product',
                'unit_price' => 4500.00,
                'cost_price' => 2881.36, // Fortune Green VH-FG-20263610
                'stock_qty' => 35.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'name' => '4mm Lapp DC Cables',
                'sku' => 'CAB-LAPP-4MM-DC',
                'category_id' => $categories['Cables & Wiring']->id,
                'type' => 'product',
                'unit_price' => 25500.00,
                'cost_price' => 16788.80, // 35m Red 8,394.40 + 35m Black 8,394.40 = 16,788.80 (VH-FG-20263633)
                'stock_qty' => 500.00,
                'alert_qty' => 50.00,
                'track_serial' => false,
                'warranty_months' => 120,
            ],
            [
                'name' => 'Earth Rod 5Ft',
                'sku' => 'SW-EARTH-ROD-5FT',
                'category_id' => $categories['Electrical Switchgear & Protection']->id,
                'type' => 'product',
                'unit_price' => 5200.00,
                'cost_price' => 3000.00, // New Sampath Electricals CAS-89424
                'stock_qty' => 60.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'name' => 'ACL Main Switch',
                'sku' => 'SW-ACL-MAIN-SWITCH-40A',
                'category_id' => $categories['Electrical Switchgear & Protection']->id,
                'type' => 'product',
                'unit_price' => 3100.00,
                'cost_price' => 2100.00, // 2 x 2,100 = 4,200.00 (Geyli Hardware Receipt 1929)
                'stock_qty' => 40.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'name' => 'Fortune Green Import Duties & Customs Clearance',
                'sku' => 'SRV-IMPORT-DUTY-FG',
                'category_id' => $categories['Logistics & Transport']->id,
                'type' => 'service',
                'unit_price' => 112000.00,
                'cost_price' => 106795.71, // 56,411.03 (VH-FG-20263633) + 50,384.68 (VH-FG-20263610)
                'stock_qty' => 999.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'name' => 'Solar System Installation, Logistics & Supervision',
                'sku' => 'SRV-SOLAR-INSTALL-OVERHEAD',
                'category_id' => $categories['Installation & Engineering Services']->id,
                'type' => 'service',
                'unit_price' => 150000.00,
                'cost_price' => 149000.00, // 35,000 install + 2,000 food + 112,000 overhead
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

        // 5. Seed Sales Agent Krishantha (3% Commission)
        $agentKrishantha = SalesAgent::updateOrCreate(
            [
                'company_id' => $company->id,
                'agent_code' => 'AGT-003',
            ],
            [
                'name' => 'Krishantha',
                'nic_number' => '198512948210',
                'phone' => '077 889 9112',
                'email' => 'krishantha.solar@example.com',
                'address' => 'No. 54/2, Negombo Road, Katana',
                'bank_name' => 'Commercial Bank',
                'bank_branch' => 'Mirigama Branch',
                'bank_account_name' => 'W. A. Krishantha',
                'bank_account_no' => '8012398471',
                'default_commission_type' => 'percentage',
                'default_commission_value' => 3.00,
                'is_active' => true,
                'notes' => 'Direct solar sales consultant for Katana & Thimbitigaskatuwa projects (3% project commission)',
            ]
        );

        // 6. Ensure Customer Mr. M.A.N.A.M. Fernando Exists
        $customerFernando = Contact::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'MR. M A N A M Fernando',
            ],
            [
                'type' => 'customer',
                'phone' => '+94 77 345 6789',
                'email' => 'manam.fernando@example.com',
                'address' => '260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa',
                'ceb_account_no' => 'CEB-RU-199-005',
                'tax_no' => 'NIC-651234567V',
                'balance' => 0.00,
            ]
        );

        // 7. Seed Quotation / Agreement VHE/2026/RU/00199/005
        $quotation = Quotation::updateOrCreate(
            [
                'company_id' => $company->id,
                'quote_number' => 'VHE/2026/RU/00199/005',
            ],
            [
                'contact_id' => $customerFernando->id,
                'sales_agent_id' => $agentKrishantha->id,
                'quote_date' => '2026-10-10',
                'valid_until' => '2026-10-25',
                'status' => 'accepted',
                'system_kw' => 5.0,
                'phase_type' => 'Single',
                'price_display_mode' => 'package_total_only',
                'subtotal' => 1250000.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 1250000.00,
                'commission_amount' => 37500.00,
                'terms' => "1. Valid for 14 days from date of issue.\n2. Milestone 1: 50% advance upon contract signing (LKR 625,000.00).\n3. Milestone 2: 50% upon successful testing & commissioning (LKR 625,000.00).\n4. Warranty: 30 Years on AE Bifacial Panels, 10 Years on Inverter & Lithium Battery, 2 Years comprehensive workmanship warranty.",
                'is_sent' => true,
                'sent_at' => '2026-10-10 10:00:00',
            ]
        );

        // 8. Seed Invoice Linked to Project VHE/2026/RU/00199/005
        // Reconcile or update existing INV-2026-0005 for Fernando
        $invoice = Invoice::updateOrCreate(
            [
                'company_id' => $company->id,
                'invoice_number' => 'INV-2026-0005',
            ],
            [
                'quotation_id' => $quotation->id,
                'contact_id' => $customerFernando->id,
                'sales_agent_id' => $agentKrishantha->id,
                'invoice_date' => '2026-10-12',
                'due_date' => '2026-10-18',
                'type' => 'milestone',
                'price_display_mode' => 'package_total_only',
                'subtotal' => 1250000.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 1250000.00,
                'paid_amount' => 1250000.00,
                'due_amount' => 0.00,
                'commission_amount' => 37500.00, // 3% of 1,250,000.00
                'commission_status' => 'paid',
                'status' => 'paid',
                'is_sent' => true,
                'sent_at' => '2026-10-12 11:30:00',
            ]
        );

        // 9. Populate the 12 Exact Costing & Invoice Items
        // Total Subtotal = LKR 1,250,000.00
        // Total Cost (COGS) = LKR 1,002,799.00
        // Agent Commission = LKR 37,500.00
        // Total Project Costs = LKR 1,040,299.00
        // Net Owner Profit = LKR 209,701.00 (16.78%)
        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        QuotationItem::where('quotation_id', $quotation->id)->delete();

        $items = [
            [
                'product_id' => $products['PANEL-AE-630W-BIFACIAL']->id,
                'description' => 'AE 630W Bifacial Solar PV Panels (8x 630W Tier-1 Modules - 5.04 kWp Array)',
                'quantity' => 8,
                'unit_price' => 38000.00,
                'cost_price' => 29237.29, // Total Cost: 233,898.32 (Fortune Green VH-FG-20263610)
                'subtotal' => 304000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['INV-OFFGRID-5KW-48V']->id,
                'description' => '5kW Pure Sine Wave Off-Grid / Hybrid Solar Inverter (48V DC, 230V AC)',
                'quantity' => 1,
                'unit_price' => 195000.00,
                'cost_price' => 146489.00, // Total Cost: 146,489.00
                'subtotal' => 195000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['BAT-LIFEPO4-51.2V-100AH']->id,
                'description' => 'LiFePO4 Lithium Battery 51.2V 100Ah Energy Storage Pack (5.12 kWh Storage)',
                'quantity' => 1,
                'unit_price' => 385000.00,
                'cost_price' => 296610.17, // Total Cost: 296,610.17 (Fortune Green VH-FG-20263633)
                'subtotal' => 385000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['MNT-D-RAIL-04']->id,
                'description' => 'D Rail 04 Solar Aluminum Mounting Rails with Stainless Tile Brackets',
                'quantity' => 4,
                'unit_price' => 8500.00,
                'cost_price' => 5762.72, // Total Cost: 23,050.88 (Fortune Green VH-FG-20263610)
                'subtotal' => 34000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SW-DC-BREAKER-BB2-40']->id,
                'description' => 'DC Circuit Breaker BB2-40 (2P 40A 550V DC Protection)',
                'quantity' => 2,
                'unit_price' => 6800.00,
                'cost_price' => 4788.14, // Total Cost: 9,576.28 (Fortune Green VH-FG-20263610)
                'subtotal' => 13600.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SW-DC-SPD-BUD-40-2R']->id,
                'description' => 'DC SPD BUD-40/2R Surge Protective Device (600V DC Arrester)',
                'quantity' => 2,
                'unit_price' => 7500.00,
                'cost_price' => 5254.24, // Total Cost: 10,508.48 (Fortune Green VH-FG-20263610)
                'subtotal' => 15000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SW-AC-SPD-BUA-40-2']->id,
                'description' => 'AC SPD BUA-40/2 Type 2 AC Surge Protective Device',
                'quantity' => 1,
                'unit_price' => 4500.00,
                'cost_price' => 2881.36, // Total Cost: 2,881.36 (Fortune Green VH-FG-20263610)
                'subtotal' => 4500.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['CAB-LAPP-4MM-DC']->id,
                'description' => '4mm Lapp DC Cables (35m Red + 35m Black UV-Resistant Solar Cable)',
                'quantity' => 1,
                'unit_price' => 25500.00,
                'cost_price' => 16788.80, // Total Cost: 16,788.80 (Fortune Green VH-FG-20263633)
                'subtotal' => 25500.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SW-EARTH-ROD-5FT']->id,
                'description' => 'Earth Rod 5Ft Copper-Bonded with Heavy Duty Grounding Clip',
                'quantity' => 1,
                'unit_price' => 5200.00,
                'cost_price' => 3000.00, // Total Cost: 3,000.00 (New Sampath Electricals CAS-89424)
                'subtotal' => 5200.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SW-ACL-MAIN-SWITCH-40A']->id,
                'description' => 'ACL Main Switch 40A Double Pole Isolator Switch',
                'quantity' => 2,
                'unit_price' => 3100.00,
                'cost_price' => 2100.00, // Total Cost: 4,200.00 (Geyli Hardware Receipt 1929)
                'subtotal' => 6200.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SRV-IMPORT-DUTY-FG']->id,
                'description' => 'Import Tax, Port Duties & Customs Clearance Surcharges (VH-FG-20263633 & 3610)',
                'quantity' => 1,
                'unit_price' => 112000.00,
                'cost_price' => 106795.71, // Total Cost: 106,795.71 (56,411.03 + 50,384.68)
                'subtotal' => 112000.00,
                'tax' => 0.00,
            ],
            [
                'product_id' => $products['SRV-SOLAR-INSTALL-OVERHEAD']->id,
                'description' => 'Turnkey Installation, Site Logistics, Food & Project Overhead Allocation',
                'quantity' => 1,
                'unit_price' => 150000.00,
                'cost_price' => 149000.00, // Total Cost: 149,000.00 (35,000 labor + 2,000 food + 112,000 overhead)
                'subtotal' => 150000.00,
                'tax' => 0.00,
            ],
        ];

        foreach ($items as $item) {
            InvoiceItem::create(array_merge($item, ['invoice_id' => $invoice->id]));
            QuotationItem::create(array_merge($item, ['quotation_id' => $quotation->id]));
        }

        // 10. Record Both Milestone Payments (LKR 625,000 x 2 = LKR 1,250,000.00)
        Payment::where('invoice_id', $invoice->id)->delete();

        // Milestone 1 (2026-10-15)
        Payment::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'payment_date' => '2026-10-15',
            'amount' => 625000.00,
            'method' => 'bank_transfer',
            'milestone_label' => '50% Advance Payment on Contract Signing',
            'reference_no' => 'TXN-DFCC-2026-1015',
            'notes' => 'Milestone 1 (50% Advance) for 5kW Solar System (VHE/2026/RU/00199/005)',
        ]);

        // Milestone 2 (2026-10-18)
        Payment::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'payment_date' => '2026-10-18',
            'amount' => 625000.00,
            'method' => 'bank_transfer',
            'milestone_label' => '50% Final Payment on Commissioning & Handover',
            'reference_no' => 'TXN-BOC-2026-1018',
            'notes' => 'Milestone 2 (50% Commissioning & Handover) for 5kW Solar System (VHE/2026/RU/00199/005)',
        ]);

        // 11. Seed Operational Expenses in expenses table
        Expense::where('company_id', $company->id)
            ->where('reference_no', 'VHE/2026/RU/00199/005')
            ->delete();

        Expense::create([
            'company_id' => $company->id,
            'category' => 'Subcontractor / Labor',
            'title' => 'Installing Charges (5 x 7,000) - Mr. M.A.N.A.M. Fernando',
            'amount' => 35000.00,
            'expense_date' => '2026-10-18',
            'reference_no' => 'VHE/2026/RU/00199/005',
            'notes' => '5 solar technicians @ LKR 7,000/day for Katana Road installation',
        ]);

        Expense::create([
            'company_id' => $company->id,
            'category' => 'Site Logistics',
            'title' => 'Site Food & Refreshments - Katana Road Site',
            'amount' => 2000.00,
            'expense_date' => '2026-10-17',
            'reference_no' => 'VHE/2026/RU/00199/005',
            'notes' => 'Field crew meals and hydration during panel installation',
        ]);

        Expense::create([
            'company_id' => $company->id,
            'category' => 'Office Overhead',
            'title' => 'Office Expenses Allocation - Project VHE/2026/RU/00199/005',
            'amount' => 112000.00,
            'expense_date' => '2026-10-18',
            'reference_no' => 'VHE/2026/RU/00199/005',
            'notes' => 'Project administrative, design, engineering & overhead cost allocation',
        ]);

        // 12. Seed Commission Payout Voucher for Krishantha (LKR 37,500.00)
        CommissionPayout::updateOrCreate(
            [
                'company_id' => $company->id,
                'voucher_number' => 'COM-VHE-2026-0002',
            ],
            [
                'sales_agent_id' => $agentKrishantha->id,
                'invoice_id' => $invoice->id,
                'payout_date' => '2026-10-19',
                'amount' => 37500.00,
                'payment_method' => 'bank_transfer',
                'reference_no' => 'TXN-COMB-2026-8841',
                'status' => 'paid',
                'notes' => '3% Commission settlement for Mr. M.A.N.A.M. Fernando Katana Road 5kW Off-Grid Project (VHE/2026/RU/00199/005)',
                'paper_size' => 'a5',
                'orientation' => 'landscape',
            ]
        );

        // 13. Reconcile / Update Completion Certificate
        CompletionCertificate::updateOrCreate(
            [
                'company_id' => $company->id,
                'certificate_number' => 'CERT-VHE/2026/RU/199/005',
            ],
            [
                'contact_id' => $customerFernando->id,
                'invoice_id' => $invoice->id,
                'quotation_id' => $quotation->id,
                'customer_name' => 'MR. M A N A M Fernando',
                'system_capacity' => '05 kW+05kw Battery offgrid Solar System',
                'site_address' => '260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa',
                'commissioning_date' => '2026-10-18',
                'grid_connection' => 'Non',
                'warranty_period' => '30 Years (Panels) / 10 Years (Inverter+Battery)',
                'system_status' => 'Commissioned & Operational',
                'verification_status' => 'Engineering Safety Compliant',
                'compliance_note' => 'Installed, tested, and commissioned in accordance with standard safety regulations.',
                'lead_engineer_name' => 'Lead Engineer',
                'lead_engineer_title' => 'Technical Operations',
                'director_name' => 'Managing Director',
                'director_title' => 'VERTEX HOME SOLUTIONS (PVT) LTD',
                'signed_date' => '2026-10-18',
                'paper_size' => 'a4',
                'orientation' => 'landscape',
                'status' => 'issued',
            ]
        );
    }
}
