<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\CommissionPayout;
use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalaryAdvance;
use App\Models\SalesAgent;
use App\Models\User;
use App\Services\PayrollCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VertexHomesSeeder extends Seeder
{
    /**
     * Run the database seeds for Vertex Home Solutions.
     */
    public function run(): void
    {
        // 1. Get or assign SaaS Plan (prefer Gold for Solar ERP + POS + Serials)
        $plan = Plan::where('slug', 'gold')->first()
            ?? Plan::where('slug', 'silver')->first()
            ?? Plan::first();

        // 2. Create / Update Tenant Company "VERTEX HOME SOLUTIONS (PVT) LTD"
        $company = Company::updateOrCreate(
            ['slug' => 'vertex-homes'],
            [
                'name' => 'VERTEX HOME SOLUTIONS (PVT) LTD',
                'plan_id' => $plan?->id,
                'subscription_ends_at' => now()->addYear(),
                'is_active' => true,
                'phone' => '+94 70 2 271 271 / +94 33 224 1241',
                'email' => 'operations@vertexhomeslk.com',
                'address' => 'No. 33/A, Hapugahagedara, Loluwagoda, Mirigama, Sri Lanka',
                'logo_path' => null,
                'tax_number' => 'TIN-10293847-001',
                'settings' => [
                    'currency' => 'LKR',
                    'currency_symbol' => 'Rs.',
                    'website' => 'vertexhomeslk.com',
                    'bank_details' => "DFCC Bank - Acc No: 101001490231 (Branch: Giriulla) - VERTEX HOME SOLUTIONS (PVT) LTD\nBank Of Ceylon (BOC) - Acc No: 97194769 (Branch: Giriulla) - VERTEX HOME SOLUTIONS (PVT) LTD",
                    'default_quote_terms' => "1. Valid for 07 days from the date of issue.\n2. 60% advance upon confirmation, 20% on delivery, 20% after commissioning.\n3. Goods once sold will be covered strictly under manufacturer warranty terms.\n4. No additional or hidden charges will be applied beyond this agreed quotation.\nFor inquiries, call: Karunanayaka - 0777 48 15 34",
                    'default_invoice_terms' => "1. Valid for 07 days from the date of issue.\n2. 60% advance upon confirmation, 20% on delivery, 20% after commissioning.\n3. Goods once sold will be covered strictly under manufacturer warranty terms.\n4. No additional or hidden charges will be applied beyond this agreed quotation.",
                    'pos_footer_note' => 'Thank you for choosing Vertex Home Solutions! Customer Care: +94 70 2 271 271',
                    'bank_accounts' => [
                        [
                            'bank' => 'DFCC Bank',
                            'account_name' => 'VERTEX HOME SOLUTIONS (PVT) LTD',
                            'account_no' => '101001490231',
                            'branch' => 'Giriulla',
                        ],
                        [
                            'bank' => 'Bank Of Ceylon (BOC)',
                            'account_name' => 'VERTEX HOME SOLUTIONS (PVT) LTD',
                            'account_no' => '97194769',
                            'branch' => 'Giriulla',
                        ],
                    ],
                    'contact_inquiry' => 'Karunanayaka - 0777 48 15 34',
                ],
            ]
        );

        // 3. Create / Update Owner User operations@vertexhomeslk.com
        $owner = User::updateOrCreate(
            ['email' => 'operations@vertexhomeslk.com'],
            [
                'name' => 'Vertex Homes Operations',
                'password' => Hash::make('Password@123'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Attach owner to Company with 'owner' role
        $company->users()->syncWithoutDetaching([
            $owner->id => ['role' => 'owner'],
        ]);

        // 4. Create / Update the 8 Categories under this company
        $categoriesData = [
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
        foreach ($categoriesData as $name => $slug) {
            $categories[$name] = Category::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'slug' => $slug,
                ],
                [
                    'name' => $name,
                ]
            );
        }

        // 5. Populate All Products, Accessories & Services with exact prices & warranties from CSV
        $catalog = [
            // --- INVERTERS ---
            [
                'category' => 'Inverters',
                'name' => 'INVERTER GW10K-SDT-30',
                'sku' => 'INV-GW10K-SDT-30',
                'type' => 'product',
                'unit_price' => 884437.50, // 4,060,375 - 716k panels - 1.36m battery - 10.5k transport - 205k services = 1,768,875 for 2 units
                'cost_price' => 740000.00,
                'stock_qty' => 10.00,
                'alert_qty' => 2.00,
                'track_serial' => true,
                'warranty_months' => 120, // 10 Years Warranty
            ],
            [
                'category' => 'Inverters',
                'name' => 'Wi-FI Dongle (Inverter Monitoring)',
                'sku' => 'INV-WIFI-DONGLE',
                'type' => 'product',
                'unit_price' => 12500.00,
                'cost_price' => 8500.00,
                'stock_qty' => 25.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],

            // --- BATTERY ENERGY STORAGE ---
            [
                'category' => 'Battery Energy Storage',
                'name' => 'Lithium Battery Pack 16kWh-(IP21)',
                'sku' => 'BAT-LITH-16KWH-IP21',
                'type' => 'product',
                'unit_price' => 680000.00,
                'cost_price' => 540000.00,
                'stock_qty' => 8.00,
                'alert_qty' => 2.00,
                'track_serial' => true,
                'warranty_months' => 120, // 10 Years Warranty
            ],

            // --- SOLAR PV PANELS ---
            [
                'category' => 'Solar PV Panels',
                'name' => 'Solar Panels METEOR 610W-630W',
                'sku' => 'PANEL-METEOR-610-630W',
                'type' => 'product',
                'unit_price' => 35800.00,
                'cost_price' => 27500.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => true,
                'warranty_months' => 360, // 30 Years Performance / Product Warranty
            ],

            // --- MOUNTING & HARDWARE ---
            [
                'category' => 'Mounting & Hardware',
                'name' => 'Relling Bar (Solar Mounting Rail)',
                'sku' => 'MNT-RELLING-BAR',
                'type' => 'product',
                'unit_price' => 7000.00,
                'cost_price' => 5200.00,
                'stock_qty' => 60.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 120,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'Tille wood (Tile Hook Mounting Bracket)',
                'sku' => 'MNT-TILLE-WOOD',
                'type' => 'product',
                'unit_price' => 1392.00,
                'cost_price' => 950.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 120,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'T Clamp (Mid Clamp)',
                'sku' => 'MNT-T-CLAMP',
                'type' => 'product',
                'unit_price' => 450.00,
                'cost_price' => 280.00,
                'stock_qty' => 300.00,
                'alert_qty' => 30.00,
                'track_serial' => false,
                'warranty_months' => 120,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'End Clamp',
                'sku' => 'MNT-END-CLAMP',
                'type' => 'product',
                'unit_price' => 380.00,
                'cost_price' => 240.00,
                'stock_qty' => 300.00,
                'alert_qty' => 30.00,
                'track_serial' => false,
                'warranty_months' => 120,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'Amano Slian 3"',
                'sku' => 'MNT-AMANO-SLIAN-3IN',
                'type' => 'product',
                'unit_price' => 35.00,
                'cost_price' => 20.00,
                'stock_qty' => 250.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => '6mm Roll Plug',
                'sku' => 'MNT-ROLL-PLUG-6MM',
                'type' => 'product',
                'unit_price' => 12.00,
                'cost_price' => 6.00,
                'stock_qty' => 1000.00,
                'alert_qty' => 100.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => '6" Wall plug',
                'sku' => 'MNT-WALL-PLUG-6IN',
                'type' => 'product',
                'unit_price' => 45.00,
                'cost_price' => 25.00,
                'stock_qty' => 400.00,
                'alert_qty' => 40.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => '1 1/4 Skru Nail',
                'sku' => 'MNT-SKRU-NAIL-1-1-4',
                'type' => 'product',
                'unit_price' => 3.00,
                'cost_price' => 1.50,
                'stock_qty' => 2000.00,
                'alert_qty' => 200.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => '1 1/4 Scruw',
                'sku' => 'MNT-SCREW-1-1-4',
                'type' => 'product',
                'unit_price' => 5.00,
                'cost_price' => 2.50,
                'stock_qty' => 2000.00,
                'alert_qty' => 200.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => '1/2 GI Clips',
                'sku' => 'MNT-GI-CLIPS-1-2',
                'type' => 'product',
                'unit_price' => 21.00,
                'cost_price' => 12.00,
                'stock_qty' => 500.00,
                'alert_qty' => 50.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => '1" GI clip',
                'sku' => 'MNT-GI-CLIP-1IN',
                'type' => 'product',
                'unit_price' => 35.00,
                'cost_price' => 20.00,
                'stock_qty' => 400.00,
                'alert_qty' => 40.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'Cilicoon (Silicon Sealant)',
                'sku' => 'MNT-CILICOON-GEN',
                'type' => 'product',
                'unit_price' => 800.00,
                'cost_price' => 550.00,
                'stock_qty' => 50.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'Silicon Blak (Black Sealant)',
                'sku' => 'MNT-SILICON-BLACK',
                'type' => 'product',
                'unit_price' => 850.00,
                'cost_price' => 600.00,
                'stock_qty' => 40.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'Silican While (White Sealant)',
                'sku' => 'MNT-SILICON-WHITE',
                'type' => 'product',
                'unit_price' => 850.00,
                'cost_price' => 600.00,
                'stock_qty' => 40.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'PVC Glue 500ml',
                'sku' => 'MNT-PVC-GLUE-500ML',
                'type' => 'product',
                'unit_price' => 1450.00,
                'cost_price' => 950.00,
                'stock_qty' => 30.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Mounting & Hardware',
                'name' => 'No Leack 2" Roll',
                'sku' => 'MNT-NO-LEAK-2IN',
                'type' => 'product',
                'unit_price' => 850.00,
                'cost_price' => 580.00,
                'stock_qty' => 45.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],

            // --- ELECTRICAL SWITCHGEAR & PROTECTION ---
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'Beny DC SPD 2 Pool',
                'sku' => 'SW-BENY-DC-SPD-2P',
                'type' => 'product',
                'unit_price' => 5500.00,
                'cost_price' => 4100.00,
                'stock_qty' => 30.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'ATS (Automatic Transfer Switch)',
                'sku' => 'SW-ATS-AUTO-SWITCH',
                'type' => 'product',
                'unit_price' => 13500.00,
                'cost_price' => 9800.00,
                'stock_qty' => 20.00,
                'alert_qty' => 3.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'Beny DC MCB 2 Pool',
                'sku' => 'SW-BENY-DC-MCB-2P',
                'type' => 'product',
                'unit_price' => 3800.00,
                'cost_price' => 2700.00,
                'stock_qty' => 35.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'Beny AC SPD 2 Pool',
                'sku' => 'SW-BENY-AC-SPD-2P',
                'type' => 'product',
                'unit_price' => 3500.00,
                'cost_price' => 2500.00,
                'stock_qty' => 35.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'Beny AC MCB 2 Pool',
                'sku' => 'SW-BENY-AC-MCB-2P',
                'type' => 'product',
                'unit_price' => 1300.00,
                'cost_price' => 880.00,
                'stock_qty' => 50.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 24,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'ATS Box (Metal)',
                'sku' => 'SW-ATS-BOX-METAL',
                'type' => 'product',
                'unit_price' => 4500.00,
                'cost_price' => 3200.00,
                'stock_qty' => 15.00,
                'alert_qty' => 3.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'ATS Box (Plastic)',
                'sku' => 'SW-ATS-BOX-PLASTIC',
                'type' => 'product',
                'unit_price' => 3200.00,
                'cost_price' => 2100.00,
                'stock_qty' => 15.00,
                'alert_qty' => 3.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'DB Box 9 Gan (9 Way)',
                'sku' => 'SW-DB-BOX-9WAY',
                'type' => 'product',
                'unit_price' => 3250.00,
                'cost_price' => 2200.00,
                'stock_qty' => 25.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'DB Box 8 Way (8 Way Boc)',
                'sku' => 'SW-DB-BOX-8WAY',
                'type' => 'product',
                'unit_price' => 2900.00,
                'cost_price' => 1950.00,
                'stock_qty' => 25.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'DB Box 6 Gan (6 Way)',
                'sku' => 'SW-DB-BOX-6WAY',
                'type' => 'product',
                'unit_price' => 2850.00,
                'cost_price' => 1900.00,
                'stock_qty' => 30.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'DB Box 4 Way',
                'sku' => 'SW-DB-BOX-4WAY',
                'type' => 'product',
                'unit_price' => 2500.00,
                'cost_price' => 1650.00,
                'stock_qty' => 35.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'DB Box 12 Way',
                'sku' => 'SW-DB-BOX-12WAY',
                'type' => 'product',
                'unit_price' => 4200.00,
                'cost_price' => 2900.00,
                'stock_qty' => 15.00,
                'alert_qty' => 3.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => 'End Rod (Earth Rood)',
                'sku' => 'SW-EARTH-ROD-END',
                'type' => 'product',
                'unit_price' => 1425.00,
                'cost_price' => 950.00,
                'stock_qty' => 40.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Electrical Switchgear & Protection',
                'name' => '10 Feet Earth Rod',
                'sku' => 'SW-EARTH-ROD-10FT',
                'type' => 'product',
                'unit_price' => 2200.00,
                'cost_price' => 1500.00,
                'stock_qty' => 30.00,
                'alert_qty' => 5.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],

            // --- CABLES & WIRING ---
            [
                'category' => 'Cables & Wiring',
                'name' => 'D/C Wire (BLACK) per Meter',
                'sku' => 'CAB-DC-WIRE-BLACK',
                'type' => 'product',
                'unit_price' => 250.00,
                'cost_price' => 180.00,
                'stock_qty' => 500.00,
                'alert_qty' => 50.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'D/C Wire (RED) per Meter',
                'sku' => 'CAB-DC-WIRE-RED',
                'type' => 'product',
                'unit_price' => 250.00,
                'cost_price' => 180.00,
                'stock_qty' => 500.00,
                'alert_qty' => 50.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'AC Wire 1M',
                'sku' => 'CAB-AC-WIRE-1M',
                'type' => 'product',
                'unit_price' => 520.00,
                'cost_price' => 380.00,
                'stock_qty' => 300.00,
                'alert_qty' => 30.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '16mm AC wire',
                'sku' => 'CAB-AC-WIRE-16MM',
                'type' => 'product',
                'unit_price' => 1850.00,
                'cost_price' => 1400.00,
                'stock_qty' => 100.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '4mm Earth Wire',
                'sku' => 'CAB-EARTH-WIRE-4MM',
                'type' => 'product',
                'unit_price' => 290.00,
                'cost_price' => 200.00,
                'stock_qty' => 300.00,
                'alert_qty' => 30.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '5mm Earth Wire',
                'sku' => 'CAB-EARTH-WIRE-5MM',
                'type' => 'product',
                'unit_price' => 350.00,
                'cost_price' => 240.00,
                'stock_qty' => 200.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '6mm Earth Wire',
                'sku' => 'CAB-EARTH-WIRE-6MM',
                'type' => 'product',
                'unit_price' => 420.00,
                'cost_price' => 300.00,
                'stock_qty' => 200.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '25*SGG Cabel',
                'sku' => 'CAB-25-SGG-CABLE',
                'type' => 'product',
                'unit_price' => 2670.00,
                'cost_price' => 1950.00,
                'stock_qty' => 100.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'Cabel Lugus 25',
                'sku' => 'CAB-LUG-25',
                'type' => 'product',
                'unit_price' => 110.00,
                'cost_price' => 65.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '6*10 Lug',
                'sku' => 'CAB-LUG-6-10',
                'type' => 'product',
                'unit_price' => 65.00,
                'cost_price' => 38.00,
                'stock_qty' => 250.00,
                'alert_qty' => 30.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'MC4 Connector Pair',
                'sku' => 'CAB-MC4-PAIR',
                'type' => 'product',
                'unit_price' => 250.00,
                'cost_price' => 140.00,
                'stock_qty' => 200.00,
                'alert_qty' => 30.00,
                'track_serial' => false,
                'warranty_months' => 60,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'Cable Tai (Cable Tie)',
                'sku' => 'CAB-CABLE-TIE-1',
                'type' => 'product',
                'unit_price' => 7.00,
                'cost_price' => 3.00,
                'stock_qty' => 2000.00,
                'alert_qty' => 200.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '10" Cable tie Pack',
                'sku' => 'CAB-CABLE-TIE-PACK-10IN',
                'type' => 'product',
                'unit_price' => 650.00,
                'cost_price' => 400.00,
                'stock_qty' => 50.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '19 PG Glan',
                'sku' => 'CAB-PG-GLAND-19',
                'type' => 'product',
                'unit_price' => 75.00,
                'cost_price' => 45.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '7 PG Glan',
                'sku' => 'CAB-PG-GLAND-7',
                'type' => 'product',
                'unit_price' => 35.00,
                'cost_price' => 20.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '2"*2" Cassing',
                'sku' => 'CAB-CASING-2X2',
                'type' => 'product',
                'unit_price' => 1800.00,
                'cost_price' => 1250.00,
                'stock_qty' => 60.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '2"*1* Cassing (2x1 Casing)',
                'sku' => 'CAB-CASING-2X1',
                'type' => 'product',
                'unit_price' => 1200.00,
                'cost_price' => 850.00,
                'stock_qty' => 60.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'PVC Lenth 1/2"',
                'sku' => 'CAB-PVC-CONDUIT-1-2',
                'type' => 'product',
                'unit_price' => 462.50,
                'cost_price' => 310.00,
                'stock_qty' => 100.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1/2 PVC Pipe',
                'sku' => 'CAB-PVC-PIPE-1-2',
                'type' => 'product',
                'unit_price' => 350.00,
                'cost_price' => 220.00,
                'stock_qty' => 100.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1\' PVC Pipe',
                'sku' => 'CAB-PVC-PIPE-1IN',
                'type' => 'product',
                'unit_price' => 550.00,
                'cost_price' => 370.00,
                'stock_qty' => 80.00,
                'alert_qty' => 10.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'PVC 1" T Shokat',
                'sku' => 'CAB-PVC-TEE-1IN',
                'type' => 'product',
                'unit_price' => 80.00,
                'cost_price' => 45.00,
                'stock_qty' => 120.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'PVC 1/2" T Shokat',
                'sku' => 'CAB-PVC-TEE-1-2',
                'type' => 'product',
                'unit_price' => 50.00,
                'cost_price' => 28.00,
                'stock_qty' => 120.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => 'PVC 1/2" Bend',
                'sku' => 'CAB-PVC-BEND-1-2',
                'type' => 'product',
                'unit_price' => 78.00,
                'cost_price' => 42.00,
                'stock_qty' => 120.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1/2" Bend 20',
                'sku' => 'CAB-PVC-BEND-1-2-20',
                'type' => 'product',
                'unit_price' => 80.00,
                'cost_price' => 45.00,
                'stock_qty' => 120.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1\' Bend (1" PVC Bend)',
                'sku' => 'CAB-PVC-BEND-1IN',
                'type' => 'product',
                'unit_price' => 110.00,
                'cost_price' => 65.00,
                'stock_qty' => 100.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1" Socket',
                'sku' => 'CAB-PVC-SOCKET-1IN',
                'type' => 'product',
                'unit_price' => 85.00,
                'cost_price' => 48.00,
                'stock_qty' => 120.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1 to 1/2 reduser Sockert',
                'sku' => 'CAB-PVC-REDUCER-1-1-2',
                'type' => 'product',
                'unit_price' => 95.00,
                'cost_price' => 55.00,
                'stock_qty' => 100.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1/2 end cap',
                'sku' => 'CAB-PVC-ENDCAP-1-2',
                'type' => 'product',
                'unit_price' => 45.00,
                'cost_price' => 25.00,
                'stock_qty' => 100.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1/2 Flexible m (Conduit)',
                'sku' => 'CAB-FLEX-CONDUIT-1-2',
                'type' => 'product',
                'unit_price' => 70.00,
                'cost_price' => 42.00,
                'stock_qty' => 200.00,
                'alert_qty' => 25.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1 Flex (1" Flexible Conduit)',
                'sku' => 'CAB-FLEX-CONDUIT-1IN',
                'type' => 'product',
                'unit_price' => 90.00,
                'cost_price' => 55.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1 1/2 Flexible m',
                'sku' => 'CAB-FLEX-CONDUIT-1-1-2',
                'type' => 'product',
                'unit_price' => 180.00,
                'cost_price' => 110.00,
                'stock_qty' => 100.00,
                'alert_qty' => 15.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Cables & Wiring',
                'name' => '1/4 Flecible m',
                'sku' => 'CAB-FLEX-CONDUIT-1-4',
                'type' => 'product',
                'unit_price' => 65.00,
                'cost_price' => 38.00,
                'stock_qty' => 150.00,
                'alert_qty' => 20.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],

            // --- INSTALLATION & ENGINEERING SERVICES ---
            [
                'category' => 'Installation & Engineering Services',
                'name' => 'Panel Installation Cost',
                'sku' => 'SRV-PANEL-INSTALL',
                'type' => 'service',
                'unit_price' => 150000.00,
                'cost_price' => 85000.00,
                'stock_qty' => 0.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 36,
            ],
            [
                'category' => 'Installation & Engineering Services',
                'name' => 'Battery Installation Cost',
                'sku' => 'SRV-BATTERY-INSTALL',
                'type' => 'service',
                'unit_price' => 10000.00,
                'cost_price' => 5000.00,
                'stock_qty' => 0.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 36,
            ],
            [
                'category' => 'Installation & Engineering Services',
                'name' => 'Service Cost / Panel (Sarvice Cost 750.00*Panel)',
                'sku' => 'SRV-PANEL-SERVICE-UNIT',
                'type' => 'service',
                'unit_price' => 750.00,
                'cost_price' => 350.00,
                'stock_qty' => 0.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Installation & Engineering Services',
                'name' => 'Solar System Insurance Policy',
                'sku' => 'SRV-INSURANCE-POLICY',
                'type' => 'service',
                'unit_price' => 15000.00,
                'cost_price' => 10000.00,
                'stock_qty' => 0.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 12,
            ],

            // --- LOGISTICS & TRANSPORT ---
            [
                'category' => 'Logistics & Transport',
                'name' => 'Transport: Kadawatha to Main Store (Vehicle 47-0258)',
                'sku' => 'TRN-KADAWATHA-MAIN-STORE',
                'type' => 'service',
                'unit_price' => 7500.00,
                'cost_price' => 6000.00,
                'stock_qty' => 0.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
            [
                'category' => 'Logistics & Transport',
                'name' => 'Transport: Main Store to Mirigama Project Site',
                'sku' => 'TRN-MAIN-STORE-MIRIGAMA',
                'type' => 'service',
                'unit_price' => 3000.00,
                'cost_price' => 2200.00,
                'stock_qty' => 0.00,
                'alert_qty' => 0.00,
                'track_serial' => false,
                'warranty_months' => 0,
            ],
        ];

        // Also dynamically parse solar_equipment.csv if present in project root
        $csvPath = base_path('solar_equipment.csv');
        if (file_exists($csvPath)) {
            $handle = fopen($csvPath, 'r');
            if ($handle !== false) {
                $rowIndex = 0;
                while (($row = fgetcsv($handle)) !== false) {
                    $rowIndex++;
                    if ($rowIndex < 14) {
                        continue;
                    }

                    // Parse Accessories Item from columns 8-11
                    $accItemName = isset($row[8]) ? trim($row[8]) : '';
                    $accUnitPriceStr = isset($row[10]) ? trim(str_replace([',', '"', ' '], '', $row[10])) : '';

                    if (! empty($accItemName) && is_numeric($accUnitPriceStr)) {
                        $unitPrice = (float) $accUnitPriceStr;
                        // Avoid adding duplicate placeholder labels
                        if (! in_array(strtolower($accItemName), ['total accessories', 'sub total (lkr):', 'total amount (lkr):'])) {
                            // Find matching entry or append
                            $exists = false;
                            foreach ($catalog as &$entry) {
                                if (strcasecmp($entry['name'], $accItemName) === 0 || str_starts_with(strtolower($entry['name']), strtolower($accItemName))) {
                                    $entry['unit_price'] = $unitPrice;
                                    $exists = true;
                                    break;
                                }
                            }
                            unset($entry);

                            if (! $exists && $unitPrice > 0) {
                                $categoryName = 'Mounting & Hardware';
                                $lower = strtolower($accItemName);
                                if (str_contains($lower, 'wire') || str_contains($lower, 'cabel') || str_contains($lower, 'cable') || str_contains($lower, 'lug') || str_contains($lower, 'pvc') || str_contains($lower, 'flex') || str_contains($lower, 'glan') || str_contains($lower, 'cassing')) {
                                    $categoryName = 'Cables & Wiring';
                                } elseif (str_contains($lower, 'spd') || str_contains($lower, 'mcb') || str_contains($lower, 'box') || str_contains($lower, 'boc') || str_contains($lower, 'ats') || str_contains($lower, 'rod')) {
                                    $categoryName = 'Electrical Switchgear & Protection';
                                }

                                $catalog[] = [
                                    'category' => $categoryName,
                                    'name' => $accItemName,
                                    'sku' => 'GEN-'.Str::upper(Str::slug(substr($accItemName, 0, 16))),
                                    'type' => 'product',
                                    'unit_price' => $unitPrice,
                                    'cost_price' => round($unitPrice * 0.70, 2),
                                    'stock_qty' => 50.00,
                                    'alert_qty' => 5.00,
                                    'track_serial' => false,
                                    'warranty_months' => 0,
                                ];
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }

        // Insert or update all catalog products
        $createdProducts = [];
        foreach ($catalog as $item) {
            $cat = $categories[$item['category']] ?? $categories['Mounting & Hardware'];
            $createdProducts[$item['sku']] = Product::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'sku' => $item['sku'],
                ],
                [
                    'category_id' => $cat->id,
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $item['cost_price'],
                    'stock_qty' => $item['stock_qty'],
                    'alert_qty' => $item['alert_qty'],
                    'track_serial' => $item['track_serial'],
                    'warranty_months' => $item['warranty_months'],
                ]
            );
        }

        // 6. Create Customer from Spreadsheet: Mr. Zuffikkar (Kollupitiya)
        $customer = Contact::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Mr. Zuffikkar',
            ],
            [
                'type' => 'customer',
                'phone' => '+94 77 748 1534',
                'email' => 'zuffikkar@example.com',
                'address' => 'Project Location: Kollupitiya, Colombo 03, Sri Lanka',
                'ceb_account_no' => 'CEB-OFFGRID-KL-00205',
                'tax_no' => 'NIC-781928374V',
                'balance' => 4060375.00,
            ]
        );

        // 7. Create Quotation VHE/2026/MAD/00205/011-B from Spreadsheet
        $quotation = Quotation::updateOrCreate(
            [
                'company_id' => $company->id,
                'quote_number' => 'VHE/2026/MAD/00205/011-B',
            ],
            [
                'contact_id' => $customer->id,
                'quote_date' => '2026-10-14',
                'valid_until' => '2026-10-21', // 7 days per terms
                'system_kw' => 12.60, // 20 * 630W = 12.6 kWp DC with 20kW Inverter capacity
                'phase_type' => 'three',
                'roof_type' => 'Tile Roof / Off-Grid Solar',
                'inverter_brand' => 'GoodWe GW10K-SDT-30 (2 Units / 20kW)',
                'panel_brand' => 'METEOR 610W-630W (20 Nos)',
                'subtotal' => 4060375.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 4060375.00,
                'terms' => "1. Valid for 07 days from the date of issue.\n2. 60% advance upon confirmation, 20% on delivery, 20% after commissioning.\n3. Goods once sold will be covered strictly under manufacturer warranty terms.\n4. No additional or hidden charges will be applied beyond this agreed quotation.\nFor inquiries, call: Karunanayaka - 0777 48 15 34",
                'status' => 'accepted',
            ]
        );

        // Add Quotation Line Items
        // Clear previous items to avoid duplication
        QuotationItem::where('quotation_id', $quotation->id)->delete();

        $invProduct = $createdProducts['INV-GW10K-SDT-30'] ?? null;
        $batProduct = $createdProducts['BAT-LITH-16KWH-IP21'] ?? null;
        $panProduct = $createdProducts['PANEL-METEOR-610-630W'] ?? null;

        if ($invProduct) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $invProduct->id,
                'description' => 'INVERTER GW10K-SDT-30 (10kW Three Phase Inverter - 10 Years Warranty)',
                'quantity' => 2,
                'unit_price' => $invProduct->unit_price,
                'subtotal' => $invProduct->unit_price * 2,
            ]);
        }

        if ($batProduct) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $batProduct->id,
                'description' => 'Lithium Battery Pack 16kWh-(IP21) (10 Years Warranty)',
                'quantity' => 2,
                'unit_price' => $batProduct->unit_price,
                'subtotal' => $batProduct->unit_price * 2,
            ]);
        }

        if ($panProduct) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $panProduct->id,
                'description' => 'Solar Panels METEOR 610W-630W (30 Years Performance / Product Warranty)',
                'quantity' => 20,
                'unit_price' => $panProduct->unit_price,
                'subtotal' => $panProduct->unit_price * 20,
            ]);
        }

        // Transport Item
        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $createdProducts['TRN-KADAWATHA-MAIN-STORE']->id ?? null,
            'description' => 'Transport: Kadawatha to Main Store & Main Store to Mirigama (Vehicle 47-0258)',
            'quantity' => 1,
            'unit_price' => 10500.00,
            'subtotal' => 10500.00,
        ]);

        // Services & Installation
        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $createdProducts['SRV-PANEL-INSTALL']->id ?? null,
            'description' => 'Turnkey Installation, DC/AC Switchgear Wiring & Battery Setup (Panel 150k + Battery 10k + Service 45k)',
            'quantity' => 1,
            'unit_price' => 205000.00,
            'subtotal' => 205000.00,
        ]);

        // 8. Sample Tracked Serials for Inverter & Battery
        if ($invProduct) {
            ProductSerial::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'serial_number' => 'GW10K-SDT-30-2026-0081',
                ],
                [
                    'product_id' => $invProduct->id,
                    'contact_id' => $customer->id,
                    'warranty_months' => 120,
                    'warranty_expires_at' => now()->addMonths(120)->toDateString(),
                    'status' => 'sold',
                    'notes' => 'Installed at Kollupitiya project (Quote: VHE/2026/MAD/00205/011-B)',
                ]
            );

            ProductSerial::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'serial_number' => 'GW10K-SDT-30-2026-0082',
                ],
                [
                    'product_id' => $invProduct->id,
                    'contact_id' => $customer->id,
                    'warranty_months' => 120,
                    'warranty_expires_at' => now()->addMonths(120)->toDateString(),
                    'status' => 'sold',
                    'notes' => 'Installed at Kollupitiya project (Quote: VHE/2026/MAD/00205/011-B)',
                ]
            );
        }

        if ($batProduct) {
            ProductSerial::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'serial_number' => 'BAT16KWH-IP21-SN-9901',
                ],
                [
                    'product_id' => $batProduct->id,
                    'contact_id' => $customer->id,
                    'warranty_months' => 120,
                    'warranty_expires_at' => now()->addMonths(120)->toDateString(),
                    'status' => 'sold',
                    'notes' => 'Installed at Kollupitiya project (Quote: VHE/2026/MAD/00205/011-B)',
                ]
            );

            ProductSerial::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'serial_number' => 'BAT16KWH-IP21-SN-9902',
                ],
                [
                    'product_id' => $batProduct->id,
                    'contact_id' => null,
                    'warranty_months' => 120,
                    'warranty_expires_at' => now()->addMonths(120)->toDateString(),
                    'status' => 'in_stock',
                    'notes' => 'Central Store Mirigama stock',
                ]
            );
        }

        // 9. Record Audit Log for Onboarding
        AuditLog::create([
            'company_id' => $company->id,
            'user_id' => $owner->id,
            'action' => 'onboarded_tenant_from_csv',
            'model_type' => Company::class,
            'model_id' => $company->id,
            'ip_address' => '127.0.0.1',
            'changes' => [
                'name' => $company->name,
                'slug' => $company->slug,
                'products_count' => count($catalog),
                'quotation_number' => $quotation->quote_number,
                'customer' => $customer->name,
                'total_amount' => 4060375.00,
            ],
            'created_at' => now(),
        ]);

        // 10. Create Sales Agents: Aradhana Fernando & Ruwan Jayasinghe
        $agentAradhana = SalesAgent::updateOrCreate(
            [
                'company_id' => $company->id,
                'agent_code' => 'AGT-001',
            ],
            [
                'name' => 'Aradhana Fernando',
                'nic_number' => '198812345678',
                'phone' => '077 123 4567',
                'email' => 'aradhana.fernando@example.com',
                'address' => 'No. 45, Station Road, Negombo',
                'bank_name' => 'Commercial Bank',
                'bank_branch' => 'Negombo Branch',
                'bank_account_name' => 'M. A. Fernando',
                'bank_account_no' => '8001234567',
                'default_commission_type' => 'percentage',
                'default_commission_value' => 5.00,
                'is_active' => true,
                'notes' => 'Senior Solar Referral Partner - Western Province',
            ]
        );

        $agentRuwan = SalesAgent::updateOrCreate(
            [
                'company_id' => $company->id,
                'agent_code' => 'AGT-002',
            ],
            [
                'name' => 'Ruwan Jayasinghe',
                'nic_number' => '199276543210',
                'phone' => '071 987 6543',
                'email' => 'ruwan.j@example.com',
                'address' => '12/4, Kandy Road, Mirigama',
                'bank_name' => 'Sampath Bank',
                'bank_branch' => 'Mirigama Branch',
                'bank_account_name' => 'R. Jayasinghe',
                'bank_account_no' => '00987654321',
                'default_commission_type' => 'fixed',
                'default_commission_value' => 35000.00,
                'is_active' => true,
                'notes' => 'Gampaha district direct introducer',
            ]
        );

        // 11. Create Customer: MR. M A N A M Fernando
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

        // 12. Create Invoice for MR. M A N A M Fernando with Sales Agent & Commission
        $invoiceFernando = Invoice::updateOrCreate(
            [
                'company_id' => $company->id,
                'invoice_number' => 'INV-2026-0005',
            ],
            [
                'contact_id' => $customerFernando->id,
                'sales_agent_id' => $agentRuwan->id,
                'invoice_date' => '2026-03-10',
                'due_date' => '2026-03-15',
                'type' => 'standard',
                'subtotal' => 1450000.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 1450000.00,
                'paid_amount' => 1450000.00,
                'due_amount' => 0.00,
                'commission_amount' => 35000.00,
                'commission_status' => 'paid',
                'status' => 'paid',
                'is_sent' => true,
                'sent_at' => now(),
            ]
        );

        InvoiceItem::where('invoice_id', $invoiceFernando->id)->delete();
        InvoiceItem::create([
            'invoice_id' => $invoiceFernando->id,
            'description' => '05 kW Solar PV Panels + 05kW Inverter System Turnkey Installation',
            'quantity' => 1,
            'unit_price' => 950000.00,
            'cost_price' => 700000.00,
            'subtotal' => 950000.00,
            'tax' => 0.00,
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoiceFernando->id,
            'description' => '05kWh Lithium Battery Energy Storage Pack',
            'quantity' => 1,
            'unit_price' => 500000.00,
            'cost_price' => 380000.00,
            'subtotal' => 500000.00,
            'tax' => 0.00,
        ]);

        // 13. Create Sample Solar Completion Certificate for MR. M A N A M Fernando
        CompletionCertificate::updateOrCreate(
            [
                'company_id' => $company->id,
                'certificate_number' => 'CERT-VHE/2026/RU/199/005',
            ],
            [
                'contact_id' => $customerFernando->id,
                'invoice_id' => $invoiceFernando->id,
                'customer_name' => 'MR. M A N A M Fernando',
                'system_capacity' => '05 kW+05kw Battery offgrid Solar System',
                'site_address' => '260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa',
                'commissioning_date' => '2026-03-15',
                'grid_connection' => 'Non',
                'warranty_period' => '30 Years (Panels) / 10 Years (Inverter+Battery)',
                'system_status' => 'Commissioned & Operational',
                'verification_status' => 'Engineering Safety Compliant',
                'compliance_note' => 'Installed, tested, and commissioned in accordance with standard safety regulations.',
                'lead_engineer_name' => 'Lead Engineer',
                'lead_engineer_title' => 'Technical Operations',
                'director_name' => 'Managing Director',
                'director_title' => 'VERTEX HOME SOLUTIONS (PVT) LTD',
                'signed_date' => '2026-03-15',
                'paper_size' => 'a4',
                'orientation' => 'landscape',
                'status' => 'issued',
            ]
        );

        // 14. Create Sample Commission Payout Voucher for Ruwan Jayasinghe
        CommissionPayout::updateOrCreate(
            [
                'company_id' => $company->id,
                'voucher_number' => 'COM-VHE-2026-0001',
            ],
            [
                'sales_agent_id' => $agentRuwan->id,
                'invoice_id' => $invoiceFernando->id,
                'payout_date' => '2026-03-16',
                'amount' => 35000.00,
                'payment_method' => 'bank_transfer',
                'reference_no' => 'TXN-SMP-2026-9901',
                'status' => 'paid',
                'notes' => 'Settlement for referral of Katana Road 5kW Off-Grid Project (Inv: INV-2026-0005)',
                'paper_size' => 'a4',
                'orientation' => 'portrait',
            ]
        );

        // 15. Seed Sample Employees for Vertex Homes ERP (Solar Technicians, Supervisors, Staff)
        $empKasun = Employee::updateOrCreate(
            [
                'company_id' => $company->id,
                'nic_number' => '199318402910',
            ],
            [
                'name' => 'Kasun Chamara Perera',
                'calling_name' => 'Kasun',
                'designation' => 'Solar Technician',
                'contact_number' => '077 234 5678',
                'daily_rate' => 3500.00,
                'basic_salary' => 85000.00,
                'fixed_allowance' => 15000.00,
                'epf_number' => 'EPF-8812',
                'bank_name' => 'Commercial Bank of Ceylon',
                'bank_branch' => 'Mirigama Branch',
                'bank_account_number' => '8001928374',
                'joined_date' => '2024-02-01',
                'is_active' => true,
            ]
        );

        $empNiroshan = Employee::updateOrCreate(
            [
                'company_id' => $company->id,
                'nic_number' => '198912049182',
            ],
            [
                'name' => 'Niroshan Bandara Dissanayake',
                'calling_name' => 'Niroshan',
                'designation' => 'Site Supervisor',
                'contact_number' => '071 882 3910',
                'daily_rate' => null,
                'basic_salary' => 115000.00,
                'fixed_allowance' => 20000.00,
                'epf_number' => 'EPF-8813',
                'bank_name' => 'DFCC Bank',
                'bank_branch' => 'Giriulla Branch',
                'bank_account_number' => '1010029384',
                'joined_date' => '2023-08-15',
                'is_active' => true,
            ]
        );

        $empPradeep = Employee::updateOrCreate(
            [
                'company_id' => $company->id,
                'nic_number' => '199510294819',
            ],
            [
                'name' => 'Pradeep Kumara Senarath',
                'calling_name' => 'Pradeep',
                'designation' => 'Electrician',
                'contact_number' => '078 991 2834',
                'daily_rate' => 3000.00,
                'basic_salary' => 75000.00,
                'fixed_allowance' => 10000.00,
                'epf_number' => 'EPF-8814',
                'bank_name' => 'Bank of Ceylon',
                'bank_branch' => 'Mirigama Branch',
                'bank_account_number' => '971988231',
                'joined_date' => '2024-05-10',
                'is_active' => true,
            ]
        );

        $empSanduni = Employee::updateOrCreate(
            [
                'company_id' => $company->id,
                'nic_number' => '199689201928',
            ],
            [
                'name' => 'Sanduni Himasha Jayawardena',
                'calling_name' => 'Sanduni',
                'designation' => 'Office Staff',
                'contact_number' => '076 449 1029',
                'daily_rate' => null,
                'basic_salary' => 65000.00,
                'fixed_allowance' => 10000.00,
                'epf_number' => 'EPF-8815',
                'bank_name' => 'Sampath Bank',
                'bank_branch' => 'Pasyala Branch',
                'bank_account_number' => '1004928172',
                'joined_date' => '2024-01-15',
                'is_active' => true,
            ]
        );

        $ownerUser = $company->users()->first();

        // 16. Seed Sample Salary Advances
        SalaryAdvance::updateOrCreate(
            [
                'company_id' => $company->id,
                'employee_id' => $empKasun->id,
                'deduct_month' => '2026-10',
            ],
            [
                'amount' => 15000.00,
                'request_date' => '2026-10-10',
                'reason' => 'Emergency medical expense advance for family hospital settlement',
                'status' => 'approved',
                'approved_by' => $ownerUser?->id,
            ]
        );

        SalaryAdvance::updateOrCreate(
            [
                'company_id' => $company->id,
                'employee_id' => $empPradeep->id,
                'deduct_month' => '2026-10',
            ],
            [
                'amount' => 10000.00,
                'request_date' => '2026-10-18',
                'reason' => 'Motorbike repair required for urgent offgrid site installation commute',
                'status' => 'pending',
                'approved_by' => null,
            ]
        );

        // 17. Seed Sample October 2026 Payroll Run via PayrollCalculationService
        app(PayrollCalculationService::class)->generateMonthlyPayroll(
            $company->id,
            '2026-10',
            $ownerUser?->id ?? 1
        );
    }
}
