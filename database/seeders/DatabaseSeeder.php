<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Subscription Plans
        $this->call(SubscriptionPlanSeeder::class);

        // 1. Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@vertexinnovationlk.com'],
            [
                'name' => 'Vertex Super Admin',
                'password' => Hash::make('Password@123'),
                'is_super_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. SaaS Plans
        $bronzePlan = Plan::updateOrCreate(
            ['slug' => 'bronze'],
            [
                'name' => 'Bronze',
                'price_monthly' => 2500.00,
                'price_yearly' => 25000.00,
                'max_users' => 2,
                'max_invoices' => 100,
                'features' => [
                    'basic_invoicing',
                    'standard_quotations',
                    'basic_inventory',
                    'email_support',
                ],
                'is_active' => true,
            ]
        );

        $silverPlan = Plan::updateOrCreate(
            ['slug' => 'silver'],
            [
                'name' => 'Silver',
                'price_monthly' => 4500.00,
                'price_yearly' => 45000.00,
                'max_users' => 5,
                'max_invoices' => 500,
                'features' => [
                    'invoicing',
                    'solar_quotation_designer',
                    'inventory_tracking',
                    'expense_management',
                    'milestone_payments',
                    'priority_support',
                ],
                'is_active' => true,
            ]
        );

        $goldPlan = Plan::updateOrCreate(
            ['slug' => 'gold'],
            [
                'name' => 'Gold',
                'price_monthly' => 7500.00,
                'price_yearly' => 75000.00,
                'max_users' => 20,
                'max_invoices' => 10000,
                'features' => [
                    'unlimited_invoicing',
                    'solar_pro_pv_sizing',
                    'pos_terminal',
                    'serial_number_tracking',
                    'warranty_management',
                    'multi_user_roles',
                    'audit_logging',
                    'api_access',
                    'dedicated_support',
                ],
                'is_active' => true,
            ]
        );

        // 3. Demo Tenant Company: Lanka Solar Solutions
        $company = Company::updateOrCreate(
            ['slug' => 'lanka-solar-solutions'],
            [
                'name' => 'Lanka Solar Solutions',
                'plan_id' => $goldPlan->id,
                'subscription_ends_at' => now()->addYear(),
                'is_active' => true,
                'phone' => '+94 11 234 5678',
                'email' => 'info@lankasolar.lk',
                'address' => 'No. 123, Galle Road, Colombo 03, Sri Lanka',
                'logo_path' => null,
                'tax_number' => 'VAT-987654321',
                'settings' => [
                    'currency' => 'LKR',
                    'currency_symbol' => 'Rs.',
                    'default_tax_rate' => 0.00,
                    'ceb_tariff_rate' => 37.00,
                    'website' => 'https://lankasolar.lk',
                ],
            ]
        );

        // 4. Demo Business Owner User
        $owner = User::updateOrCreate(
            ['email' => 'owner@lankasolar.lk'],
            [
                'name' => 'Sunil Bandara',
                'password' => Hash::make('Password@123'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Attach owner to Lanka Solar Solutions
        $company->users()->syncWithoutDetaching([
            $owner->id => ['role' => 'owner'],
        ]);

        // 5. Product Categories
        $catPanels = Category::create([
            'company_id' => $company->id,
            'name' => 'Solar PV Panels',
            'slug' => 'solar-pv-panels',
        ]);

        $catInverters = Category::create([
            'company_id' => $company->id,
            'name' => 'Solar Inverters',
            'slug' => 'solar-inverters',
        ]);

        $catMounting = Category::create([
            'company_id' => $company->id,
            'name' => 'Mounting & Electrical Accessories',
            'slug' => 'mounting-accessories',
        ]);

        $catServices = Category::create([
            'company_id' => $company->id,
            'name' => 'Installation & Engineering Services',
            'slug' => 'installation-services',
        ]);

        // 6. Products & Services
        $panel = Product::create([
            'company_id' => $company->id,
            'category_id' => $catPanels->id,
            'name' => 'Jinko Tiger Neo 585W N-Type Bifacial Solar Panel',
            'sku' => 'JKM585N-72HL4-BDV',
            'type' => 'product',
            'unit_price' => 38500.00,
            'cost_price' => 31000.00,
            'stock_qty' => 150.00,
            'alert_qty' => 20.00,
            'track_serial' => true,
            'warranty_months' => 144, // 12 years product, 30 years performance
        ]);

        $inverter = Product::create([
            'company_id' => $company->id,
            'category_id' => $catInverters->id,
            'name' => 'Huawei SUN2000-10KTL-M1 10kW Three Phase Hybrid Inverter',
            'sku' => 'HUA-SUN2000-10KTL',
            'type' => 'product',
            'unit_price' => 520000.00,
            'cost_price' => 440000.00,
            'stock_qty' => 10.00,
            'alert_qty' => 2.00,
            'track_serial' => true,
            'warranty_months' => 120, // 10 years
        ]);

        $deyeInverter = Product::create([
            'company_id' => $company->id,
            'category_id' => $catInverters->id,
            'name' => 'Deye 5kW Single Phase Grid-Tied Inverter',
            'sku' => 'DEYE-SUN-5K-G05',
            'type' => 'product',
            'unit_price' => 245000.00,
            'cost_price' => 205000.00,
            'stock_qty' => 15.00,
            'alert_qty' => 3.00,
            'track_serial' => true,
            'warranty_months' => 60, // 5 years
        ]);

        $mountingKit = Product::create([
            'company_id' => $company->id,
            'category_id' => $catMounting->id,
            'name' => 'Anodized Aluminum Mounting Structure (10kW Tile Roof)',
            'sku' => 'MNT-TILE-10KW',
            'type' => 'product',
            'unit_price' => 85000.00,
            'cost_price' => 65000.00,
            'stock_qty' => 25.00,
            'alert_qty' => 5.00,
            'track_serial' => false,
            'warranty_months' => 120,
        ]);

        $installService = Product::create([
            'company_id' => $company->id,
            'category_id' => $catServices->id,
            'name' => 'Turnkey 10kW Solar PV Rooftop Installation & CEB Commissioning',
            'sku' => 'SRV-INS-10KW',
            'type' => 'service',
            'unit_price' => 150000.00,
            'cost_price' => 80000.00,
            'stock_qty' => 0.00,
            'alert_qty' => 0.00,
            'track_serial' => false,
            'warranty_months' => 36,
        ]);

        // 7. Customer Contact
        $customer = Contact::create([
            'company_id' => $company->id,
            'type' => 'customer',
            'name' => 'Kavinda Wickramasinghe',
            'phone' => '+94 77 345 6789',
            'email' => 'kavinda.w@example.com',
            'address' => '42, Peradeniya Road, Kandy, Sri Lanka',
            'ceb_account_no' => '0781294012',
            'tax_no' => 'NIC-882341908V',
            'balance' => 650000.00,
        ]);

        // 8. Solar Quotation
        $quotation = Quotation::create([
            'company_id' => $company->id,
            'contact_id' => $customer->id,
            'quote_number' => 'QUO-2026-0001',
            'quote_date' => now()->subDays(5)->toDateString(),
            'valid_until' => now()->addDays(25)->toDateString(),
            'system_kw' => 10.53,
            'phase_type' => 'three',
            'roof_type' => 'Tile Roof',
            'inverter_brand' => 'Huawei SUN2000-10KTL-M1',
            'panel_brand' => 'Jinko Tiger Neo 585W (18 Nos)',
            'subtotal' => 1363000.00,
            'discount' => 63000.00,
            'tax' => 0.00,
            'grand_total' => 1300000.00,
            'terms' => 'Milestone Payment Terms: 50% Advance with confirmation, 40% upon equipment delivery at site, 10% after CEB net-metering grid connection.',
            'status' => 'accepted',
        ]);

        // Quotation Items
        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $panel->id,
            'description' => '18 x Jinko Tiger Neo 585W N-Type Bifacial Solar Panels (10.53 kWp DC)',
            'quantity' => 18,
            'unit_price' => 38500.00,
            'subtotal' => 693000.00,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $inverter->id,
            'description' => '1 x Huawei SUN2000-10KTL-M1 10kW Three Phase Smart PV Inverter',
            'quantity' => 1,
            'unit_price' => 520000.00,
            'subtotal' => 520000.00,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $mountingKit->id,
            'description' => '1 x Heavy-Duty Anodized Aluminum Rail & Clamp Set for Tile Roof',
            'quantity' => 1,
            'unit_price' => 85000.00,
            'subtotal' => 85000.00,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $installService->id,
            'description' => 'Turnkey Installation, DC/AC Cabling, Surge Protection & CEB Testing',
            'quantity' => 1,
            'unit_price' => 65000.00,
            'subtotal' => 65000.00,
        ]);

        // 9. Invoice & Milestone Payments
        $invoice = Invoice::create([
            'company_id' => $company->id,
            'contact_id' => $customer->id,
            'quotation_id' => $quotation->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => now()->subDays(2)->toDateString(),
            'due_date' => now()->addDays(12)->toDateString(),
            'type' => 'milestone',
            'subtotal' => 1363000.00,
            'discount' => 63000.00,
            'tax' => 0.00,
            'grand_total' => 1300000.00,
            'paid_amount' => 650000.00,
            'due_amount' => 650000.00,
            'status' => 'partial',
        ]);

        // Invoice Items
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $panel->id,
            'description' => '18 x Jinko Tiger Neo 585W N-Type Bifacial Solar Panels (10.53 kWp DC)',
            'quantity' => 18,
            'unit_price' => 38500.00,
            'subtotal' => 693000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $inverter->id,
            'description' => '1 x Huawei SUN2000-10KTL-M1 10kW Three Phase Smart PV Inverter',
            'quantity' => 1,
            'unit_price' => 520000.00,
            'subtotal' => 520000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $mountingKit->id,
            'description' => '1 x Heavy-Duty Anodized Aluminum Rail & Clamp Set for Tile Roof',
            'quantity' => 1,
            'unit_price' => 85000.00,
            'subtotal' => 85000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $installService->id,
            'description' => 'Turnkey Installation, DC/AC Cabling, Surge Protection & CEB Testing',
            'quantity' => 1,
            'unit_price' => 65000.00,
            'subtotal' => 65000.00,
        ]);

        // Payment 1: 50% Advance
        Payment::create([
            'company_id' => $company->id,
            'invoice_id' => $invoice->id,
            'payment_date' => now()->subDays(2)->toDateString(),
            'amount' => 650000.00,
            'method' => 'bank_transfer',
            'reference_no' => 'BOC-TXN-884920',
            'milestone_label' => '50% Advance on Agreement Signing',
            'notes' => 'Received via Bank of Ceylon Online Banking. Official Receipt #REC-1001 issued.',
        ]);

        // 10. Sample Operational Expense
        Expense::create([
            'company_id' => $company->id,
            'category' => 'Hardware & Tools',
            'title' => 'DC Breakers, MC4 Connectors & 6mm Solar PV Cable Drums',
            'amount' => 48500.00,
            'expense_date' => now()->subDays(3)->toDateString(),
            'reference_no' => 'EXP-2026-0012',
            'notes' => 'Purchased from Colombo Solar Wholesalers for Kandy installation project.',
        ]);

        // 11. Sample Audit Log
        AuditLog::create([
            'company_id' => $company->id,
            'user_id' => $owner->id,
            'action' => 'created',
            'model_type' => 'App\\Models\\Quotation',
            'model_id' => $quotation->id,
            'ip_address' => '127.0.0.1',
            'changes' => [
                'quote_number' => 'QUO-2026-0001',
                'customer' => 'Kavinda Wickramasinghe',
                'grand_total' => 1300000.00,
                'status' => 'accepted',
            ],
            'created_at' => now()->subDays(5),
        ]);

        // 12. Sample Product Serials & Warranties
        ProductSerial::create([
            'company_id' => $company->id,
            'product_id' => $inverter->id,
            'invoice_id' => $invoice->id,
            'contact_id' => $customer->id,
            'serial_number' => 'SN-HUA-SUN2000-008492',
            'warranty_months' => 120,
            'warranty_expires_at' => now()->addMonths(120)->toDateString(),
            'status' => 'sold',
            'notes' => '10-Year Manufacturer Warranty on Huawei SUN2000-10KTL Inverter',
        ]);

        ProductSerial::create([
            'company_id' => $company->id,
            'product_id' => $deyeInverter->id,
            'invoice_id' => null,
            'contact_id' => null,
            'serial_number' => 'SN-DEYE-5K-001249',
            'warranty_months' => 60,
            'warranty_expires_at' => now()->addMonths(60)->toDateString(),
            'status' => 'in_stock',
            'notes' => 'In stock in Colombo Central Warehouse',
        ]);

        // 13. Vertex Homes Tenant & Equipment Catalog Seeder
        $this->call(VertexHomesSeeder::class);
    }
}
