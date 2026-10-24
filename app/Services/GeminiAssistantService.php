<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAssistantService
{
    protected string $apiKey;

    protected string $model;

    protected string $systemPrompt;

    public function __construct(?string $apiKey = null, ?string $model = null)
    {
        $this->apiKey = $apiKey ?: (string) (config('services.gemini.api_key') ?: env('GEMINI_API_KEY', ''));
        $this->model = $model ?: (string) (config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-2.0-flash'));
        $this->systemPrompt = "You are the Pixel Craft Studios AI Assistant for Vertex Books ERP. You provide business insights, project costing, profit calculations, and inventory data strictly scoped to the user's company. Respond fluently in the user's input language (Sinhala or English). Always format amounts in LKR with clear breakdowns.";
    }

    /**
     * Send messages to Gemini with autonomous function calling.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chat(array $messages, ?int $companyId = null): string
    {
        $companyId = $companyId ?: (Filament::getTenant()?->id ?? auth()->user()?->current_company_id ?? 1);

        if (empty($this->apiKey)) {
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in your environment.';
        }

        // Convert messages to Gemini API contents format
        $contents = [];
        foreach ($messages as $msg) {
            $role = ($msg['role'] === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['content']],
                ],
            ];
        }

        $toolDeclarations = $this->getToolDeclarations();
        $iterations = 0;
        $maxIterations = 5;

        while ($iterations < $maxIterations) {
            $iterations++;

            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $this->systemPrompt],
                    ],
                ],
                'contents' => $contents,
                'tools' => [
                    [
                        'function_declarations' => $toolDeclarations,
                    ],
                ],
            ];

            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
                $response = Http::withoutVerifying()
                    ->timeout(90)
                    ->connectTimeout(15)
                    ->withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                            CURLOPT_TCP_NODELAY => 1,
                        ],
                    ])
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($endpoint, $payload);

                if (! $response->successful()) {
                    Log::warning('Gemini API request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    $errorBody = $response->json();
                    $errorMsg = $errorBody['error']['message'] ?? 'Unable to process request with Gemini API.';

                    return "I encountered an error connecting to the AI service: {$errorMsg}";
                }

                $data = $response->json();
                $candidate = $data['candidates'][0] ?? null;
                $parts = $candidate['content']['parts'] ?? [];

                if (empty($parts)) {
                    return 'I received an empty response. How else may I assist you?';
                }

                // Check if candidate returned function call(s)
                $functionCallParts = [];
                foreach ($parts as $part) {
                    if (isset($part['functionCall'])) {
                        $functionCallParts[] = $part['functionCall'];
                    }
                }

                if (! empty($functionCallParts)) {
                    // Retain thought_signature, thought parts, and all original metadata!
                    $modelContent = $candidate['content'] ?? null;
                    if ($modelContent) {
                        $contents[] = $modelContent;
                    }

                    $responseParts = [];
                    foreach ($functionCallParts as $fCall) {
                        $fnName = $fCall['name'] ?? '';
                        $fnArgs = $fCall['args'] ?? [];

                        // Execute local tool securely scoped to company
                        $toolResult = $this->executeTool($fnName, $fnArgs, $companyId);

                        $responseParts[] = [
                            'functionResponse' => [
                                'name' => $fnName,
                                'response' => [
                                    'name' => $fnName,
                                    'content' => $toolResult,
                                ],
                            ],
                        ];
                    }

                    // Append the function response(s) turn to conversation history
                    $contents[] = [
                        'role' => 'user',
                        'parts' => $responseParts,
                    ];

                    // Continue loop to let Gemini format the answer
                    continue;
                }

                // Normal text response
                $textParts = [];
                foreach ($parts as $part) {
                    if (isset($part['text'])) {
                        $textParts[] = $part['text'];
                    }
                }

                return implode("\n", $textParts);

            } catch (\Throwable $e) {
                Log::error('Gemini Assistant Error: '.$e->getMessage(), ['exception' => $e]);

                if (str_contains($e->getMessage(), 'cURL error 28') || str_contains(strtolower($e->getMessage()), 'timed out')) {
                    return 'The request timed out while communicating with the AI service. Please try asking again or make your query more specific.';
                }

                return 'An error occurred while communicating with the Pixel Craft Studios AI Assistant: '.$e->getMessage();
            }
        }

        return 'The request required too many operations. Please ask a more specific question.';
    }

    /**
     * Dispatch tool call to the corresponding local query method.
     *
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    public function executeTool(string $name, array $args, int $companyId): array
    {
        return match ($name) {
            'get_project_profit_analysis' => $this->getProjectProfitAnalysis(
                (string) ($args['search_query'] ?? ''),
                $companyId
            ),
            'get_financial_kpi_summary' => $this->getFinancialKpiSummary(
                (string) ($args['period'] ?? 'all_time'),
                $companyId
            ),
            'get_unpaid_invoices' => $this->getUnpaidInvoices($companyId),
            'get_inventory_status' => $this->getInventoryStatus(
                isset($args['category']) ? (string) $args['category'] : null,
                $companyId
            ),
            'get_customer_overview' => $this->getCustomerOverview(
                (string) ($args['customer_name'] ?? ''),
                $companyId
            ),
            default => [
                'error' => "Unknown tool: {$name}",
            ],
        };
    }

    /**
     * Tool 1: Project Profit Analysis.
     * Fetches revenue, itemized material bills, installation labor, agent commission, office overhead, and net owner profit.
     *
     * @return array<string, mixed>
     */
    public function getProjectProfitAnalysis(string $searchQuery, int $companyId): array
    {
        $query = Invoice::with([
            'contact',
            'quotation',
            'salesAgent',
            'items.product',
            'expenses',
        ])
            ->where('company_id', $companyId)
            ->where('status', '!=', 'cancelled');

        if (! empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('invoice_number', 'like', "%{$searchQuery}%")
                    ->orWhereHas('contact', function ($cq) use ($searchQuery) {
                        $cq->where('name', 'like', "%{$searchQuery}%")
                            ->orWhere('phone', 'like', "%{$searchQuery}%")
                            ->orWhere('ceb_account_no', 'like', "%{$searchQuery}%");
                    })
                    ->orWhereHas('quotation', function ($qq) use ($searchQuery) {
                        $qq->where('quote_number', 'like', "%{$searchQuery}%");
                    });
            });
        }

        $invoice = $query->latest('id')->first();

        // If no invoice found, check if a quotation exists
        if (! $invoice) {
            $quotation = Quotation::with(['contact', 'salesAgent', 'items.product'])
                ->where('company_id', $companyId)
                ->where(function ($q) use ($searchQuery) {
                    $q->where('quote_number', 'like', "%{$searchQuery}%")
                        ->orWhereHas('contact', fn ($cq) => $cq->where('name', 'like', "%{$searchQuery}%"));
                })
                ->latest('id')
                ->first();

            if (! $quotation) {
                return [
                    'found' => false,
                    'message' => "No project, invoice, or quotation found matching '{$searchQuery}' in this company.",
                ];
            }

            return [
                'found' => true,
                'type' => 'quotation_estimate',
                'quote_number' => $quotation->quote_number,
                'customer_name' => $quotation->contact?->name,
                'system_kw' => (float) $quotation->system_kw,
                'contract_revenue_lkr' => (float) $quotation->grand_total,
                'estimated_materials_cost_lkr' => (float) $quotation->total_cost,
                'agent_commission_lkr' => (float) $quotation->commission_amount,
                'estimated_net_profit_lkr' => (float) $quotation->net_profit,
                'estimated_profit_margin_pct' => (float) $quotation->profit_margin_percentage,
                'status' => $quotation->status,
            ];
        }

        $contractValue = (float) ($invoice->grand_total ?? 0);
        $collectedAmount = (float) ($invoice->paid_amount ?? 0);
        $balanceDue = (float) ($invoice->due_amount ?? 0);

        $linkedExpenses = $invoice->expenses ?? collect();
        $externalExpenses = $linkedExpenses->where('is_internal_overhead', false);
        $internalExpenses = $linkedExpenses->where('is_internal_overhead', true);

        // 1. Direct Materials & Hardware Bills
        $materialsCost = (float) $externalExpenses
            ->filter(fn ($e) => preg_match('/material|hardware|equipment|tool|cable|component|solar|panel|inverter/i', (string) $e->category))
            ->sum('amount');

        // Fallback to item cost prices if no external material voucher is logged yet
        if ($materialsCost <= 0 && (float) $invoice->total_cost > 0) {
            $itemMaterialsCost = (float) $invoice->items
                ->filter(fn ($item) => ! preg_match('/turnkey|installation|overhead/i', (string) $item->description))
                ->sum(fn ($item) => (float) ($item->quantity * ($item->cost_price ?: $item->product?->cost_price ?: 0)));

            $materialsCost = $itemMaterialsCost > 0 ? $itemMaterialsCost : (float) $invoice->total_cost;
        }

        // 2. Installation Labour & Logistics
        $labourCost = (float) $externalExpenses
            ->filter(fn ($e) => preg_match('/labour|labor|installation|contractor|technician|travel|food|transport|logistics|site/i', (string) $e->category))
            ->sum('amount');

        $otherExternalCost = (float) $externalExpenses
            ->filter(fn ($e) => ! preg_match('/material|hardware|equipment|tool|cable|component|solar|panel|inverter|labour|labor|installation|contractor|technician|travel|food|transport|logistics|site|commission/i', (string) $e->category) && ! preg_match('/commission/i', (string) $e->title))
            ->sum('amount');

        $labourAndOps = round($labourCost + $otherExternalCost, 2);

        // 3. Agent Commission
        $commissionAmount = (float) ($invoice->commission_amount ?? 0);

        // Direct Outflow
        $directExternalOutflow = round($materialsCost + $labourAndOps + $commissionAmount, 2);

        // Internal Office Overhead (Internal absorption)
        $internalCompanyOverhead = round((float) $internalExpenses->sum('amount'), 2);

        // Retained Margin & Net Owner Profit
        $companyRetainedMargin = round($contractValue - $directExternalOutflow, 2);
        $retainedMarginPercentage = $contractValue > 0 ? round(($companyRetainedMargin / $contractValue) * 100, 1) : 0.0;
        $totalProjectCost = round($directExternalOutflow + $internalCompanyOverhead, 2);
        $netOwnerProfit = round($contractValue - $totalProjectCost, 2);
        $netMarginPercentage = $contractValue > 0 ? round(($netOwnerProfit / $contractValue) * 100, 1) : 0.0;

        $itemizedExpenses = $linkedExpenses->map(function ($e) {
            return [
                'title' => $e->title,
                'category' => $e->category,
                'amount_lkr' => (float) $e->amount,
                'date' => (string) $e->expense_date,
                'is_internal_overhead' => (bool) $e->is_internal_overhead,
            ];
        })->values()->toArray();

        return [
            'found' => true,
            'type' => 'invoice_profit_analysis',
            'customer_name' => $invoice->contact?->name,
            'customer_phone' => $invoice->contact?->phone,
            'ceb_account_no' => $invoice->contact?->ceb_account_no,
            'invoice_number' => $invoice->invoice_number,
            'quotation_number' => $invoice->quotation?->quote_number,
            'sales_agent' => $invoice->salesAgent?->name,
            'contract_revenue_lkr' => $contractValue,
            'cash_collected_lkr' => $collectedAmount,
            'balance_due_lkr' => $balanceDue,
            'collection_percentage' => $contractValue > 0 ? round(($collectedAmount / $contractValue) * 100, 1) : 0.0,
            'itemized_material_bills_lkr' => round($materialsCost, 2),
            'installation_labour_lkr' => $labourAndOps,
            'agent_commission_lkr' => $commissionAmount,
            'direct_external_outflow_lkr' => $directExternalOutflow,
            'office_overhead_lkr' => $internalCompanyOverhead,
            'company_retained_margin_lkr' => $companyRetainedMargin,
            'retained_margin_percentage' => $retainedMarginPercentage,
            'total_project_cost_lkr' => $totalProjectCost,
            'net_owner_profit_lkr' => $netOwnerProfit,
            'net_profit_margin_percentage' => $netMarginPercentage,
            'is_profitable' => $netOwnerProfit >= 0,
            'itemized_expenses' => $itemizedExpenses,
        ];
    }

    /**
     * Tool 2: Financial KPI Summary.
     * Fetches total invoiced sales, cash collected, total expenses, retained margin, and net profit.
     *
     * @return array<string, mixed>
     */
    public function getFinancialKpiSummary(string $period = 'all_time', int $companyId = 1): array
    {
        switch ($period) {
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth()->toDateString();
                $endDate = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'this_quarter':
                $startDate = Carbon::now()->startOfQuarter()->toDateString();
                $endDate = Carbon::now()->endOfQuarter()->toDateString();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear()->toDateString();
                $endDate = Carbon::now()->endOfYear()->toDateString();
                break;
            case 'all_time':
            default:
                $startDate = '2020-01-01';
                $endDate = Carbon::now()->addYear()->toDateString();
                $period = 'all_time';
                break;
        }

        // Invoices aggregation
        $invoiceSums = DB::table('invoices')
            ->where('company_id', $companyId)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('
                COALESCE(SUM(grand_total), 0) as invoiced_sales,
                COALESCE(SUM(subtotal), 0) as gross_sales,
                COALESCE(SUM(profit_markup), 0) as total_profit_markup,
                COALESCE(SUM(commission_amount), 0) as agent_commissions
            ')->first();

        $invoicedSales = round((float) ($invoiceSums?->invoiced_sales ?? 0.0), 2);
        $totalProfitMarkup = round((float) ($invoiceSums?->total_profit_markup ?? 0.0), 2);
        $agentCommissions = round((float) ($invoiceSums?->agent_commissions ?? 0.0), 2);

        // Payments aggregation
        $paymentsCollected = round((float) DB::table('payments')
            ->where('company_id', $companyId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount'), 2);

        // COGS
        $cogs = round((float) DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftJoin('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.company_id', $companyId)
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled')
            ->sum(DB::raw('invoice_items.quantity * COALESCE(NULLIF(invoice_items.cost_price, 0), products.cost_price, 0)')), 2);

        $materialCategories = [
            'Materials', 'Solar Equipment', 'Hardware', 'Hardware & Tools',
            'Components', 'Mounting & Hardware', 'Solar PV Panels', 'Inverters', 'Battery Energy Storage',
        ];

        // Internal overhead
        $internalOverhead = round((float) DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_internal_overhead', 1)
            ->sum('amount'), 2);

        // Unlinked OpEx
        $unlinkedOpEx = (float) DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_internal_overhead', 0)
            ->whereNull('invoice_id')
            ->sum('amount');

        // Invoices with turnkey captured
        $invoicesWithTurnkeyCaptured = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.company_id', $companyId)
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled')
            ->where('invoice_items.cost_price', '>', 0)
            ->where(function ($q) {
                $q->where('invoice_items.description', 'like', '%turnkey%')
                    ->orWhere('invoice_items.description', 'like', '%overhead allocation%');
            })
            ->pluck('invoices.id')
            ->unique()
            ->toArray();

        // Linked OpEx
        $linkedOpExQuery = DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->where('is_internal_overhead', 0)
            ->whereNotNull('invoice_id')
            ->whereNotIn('category', array_merge($materialCategories, ['Commission']));

        if (! empty($invoicesWithTurnkeyCaptured)) {
            $linkedOpExQuery->whereNotIn('invoice_id', $invoicesWithTurnkeyCaptured);
        }
        $linkedOpEx = (float) $linkedOpExQuery->sum('amount');

        $operatingExpenses = round($unlinkedOpEx + $linkedOpEx, 2);
        $totalExpenses = round($operatingExpenses + $internalOverhead, 2);

        $grossProfit = round($invoicedSales - $cogs, 2);
        $retainedMargin = round($invoicedSales - ($cogs + $operatingExpenses), 2);
        $netProfit = round($grossProfit - $operatingExpenses - $agentCommissions, 2);
        $netMarginPercentage = $invoicedSales > 0 ? round(($netProfit / $invoicedSales) * 100, 1) : 0.0;

        $pendingCashToCollect = round((float) DB::table('invoices')
            ->where('company_id', $companyId)
            ->where('status', '!=', 'cancelled')
            ->sum('due_amount'), 2);

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_invoiced_sales_lkr' => $invoicedSales,
            'cash_payments_collected_lkr' => $paymentsCollected,
            'cost_of_goods_sold_lkr' => $cogs,
            'operating_expenses_lkr' => $operatingExpenses,
            'internal_office_overhead_lkr' => $internalOverhead,
            'total_expenses_lkr' => $totalExpenses,
            'agent_commissions_lkr' => $agentCommissions,
            'profit_markup_included_lkr' => $totalProfitMarkup,
            'gross_profit_lkr' => $grossProfit,
            'retained_margin_lkr' => $retainedMargin,
            'net_profit_lkr' => $netProfit,
            'net_profit_margin_percentage' => $netMarginPercentage,
            'pending_cash_to_collect_lkr' => $pendingCashToCollect,
            'is_profitable' => $netProfit >= 0,
        ];
    }

    /**
     * Tool 3: Get Unpaid Invoices.
     * Lists pending/overdue invoices, customer names, contact numbers, and balance dues.
     *
     * @return array<string, mixed>
     */
    public function getUnpaidInvoices(int $companyId): array
    {
        $invoices = Invoice::with(['contact:id,name,phone,email,ceb_account_no'])
            ->where('company_id', $companyId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_amount', '>', 0)
            ->orderByDesc('due_amount')
            ->get();

        $today = Carbon::today();
        $totalBalanceDue = 0.0;

        $items = $invoices->map(function ($invoice) use ($today, &$totalBalanceDue) {
            $due = (float) $invoice->due_amount;
            $totalBalanceDue += $due;

            $daysOverdue = 0;
            if ($invoice->due_date && Carbon::parse($invoice->due_date)->isPast()) {
                $daysOverdue = Carbon::parse($invoice->due_date)->diffInDays($today);
            }

            return [
                'invoice_number' => $invoice->invoice_number,
                'customer_name' => $invoice->contact?->name ?? 'Unknown Customer',
                'contact_number' => $invoice->contact?->phone ?? 'N/A',
                'ceb_account_no' => $invoice->contact?->ceb_account_no ?? 'N/A',
                'invoice_date' => (string) $invoice->invoice_date,
                'due_date' => (string) $invoice->due_date,
                'grand_total_lkr' => (float) $invoice->grand_total,
                'paid_amount_lkr' => (float) $invoice->paid_amount,
                'balance_due_lkr' => $due,
                'status' => $invoice->status,
                'days_overdue' => $daysOverdue,
            ];
        })->toArray();

        return [
            'total_unpaid_count' => count($items),
            'total_balance_due_lkr' => round($totalBalanceDue, 2),
            'unpaid_invoices' => $items,
        ];
    }

    /**
     * Tool 4: Get Inventory Status.
     * Lists stock on hand, serial numbers, and cost prices for Panels, Inverters, Batteries, and BOS accessories.
     *
     * @return array<string, mixed>
     */
    public function getInventoryStatus(?string $category = null, int $companyId = 1): array
    {
        $query = Product::with(['category', 'serials'])
            ->where('company_id', $companyId);

        if (! empty($category)) {
            $query->where(function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%")
                    ->orWhere('sku', 'like', "%{$category}%")
                    ->orWhere('type', 'like', "%{$category}%")
                    ->orWhereHas('category', function ($cq) use ($category) {
                        $cq->where('name', 'like', "%{$category}%");
                    });
            });
        }

        $products = $query->orderBy('name')->get();
        $totalValuation = 0.0;

        $items = $products->map(function ($product) use (&$totalValuation) {
            $stock = (float) $product->stock_qty;
            $cost = (float) $product->cost_price;
            $lineValue = $stock * $cost;
            $totalValuation += $lineValue;

            $serials = $product->serials
                ->filter(fn ($s) => in_array($s->status, ['in_stock', 'available', 'active']))
                ->pluck('serial_number')
                ->values()
                ->toArray();

            return [
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category?->name ?? 'Uncategorized',
                'type' => $product->type,
                'stock_qty' => $stock,
                'unit_price_lkr' => (float) $product->unit_price,
                'cost_price_lkr' => $cost,
                'total_cost_value_lkr' => round($lineValue, 2),
                'track_serial' => (bool) $product->track_serial,
                'available_serials' => $serials,
                'warranty' => $product->formatted_warranty ?? 'N/A',
            ];
        })->toArray();

        return [
            'category_filter' => $category ?: 'all',
            'product_count' => count($items),
            'total_inventory_cost_value_lkr' => round($totalValuation, 2),
            'products' => $items,
        ];
    }

    /**
     * Tool 5: Get Customer Overview.
     * Returns customer projects, agreement dates, kW capacities, and warranty terms.
     *
     * @return array<string, mixed>
     */
    public function getCustomerOverview(string $customerName, int $companyId): array
    {
        $contact = Contact::with([
            'quotations' => fn ($q) => $q->latest('id'),
            'invoices' => fn ($q) => $q->latest('id'),
            'serials.product',
        ])
            ->where('company_id', $companyId)
            ->where(function ($q) use ($customerName) {
                $q->where('name', 'like', "%{$customerName}%")
                    ->orWhere('phone', 'like', "%{$customerName}%")
                    ->orWhere('ceb_account_no', 'like', "%{$customerName}%");
            })
            ->first();

        if (! $contact) {
            return [
                'customer_found' => false,
                'message' => "No customer found matching '{$customerName}' in this company.",
            ];
        }

        $projects = $contact->quotations->map(function ($quote) {
            return [
                'quote_number' => $quote->quote_number,
                'system_kw' => (float) $quote->system_kw,
                'phase_type' => $quote->phase_type,
                'panel_brand' => $quote->panel_brand,
                'inverter_brand' => $quote->inverter_brand,
                'grand_total_lkr' => (float) $quote->grand_total,
                'status' => $quote->status,
                'quote_date' => (string) $quote->quote_date,
            ];
        })->toArray();

        $invoices = $contact->invoices->map(function ($inv) {
            return [
                'invoice_number' => $inv->invoice_number,
                'grand_total_lkr' => (float) $inv->grand_total,
                'paid_amount_lkr' => (float) $inv->paid_amount,
                'due_amount_lkr' => (float) $inv->due_amount,
                'status' => $inv->status,
                'invoice_date' => (string) $inv->invoice_date,
            ];
        })->toArray();

        $equipmentSerials = $contact->serials->map(function ($serial) {
            return [
                'serial_number' => $serial->serial_number,
                'product_name' => $serial->product?->name ?? 'Equipment',
                'warranty_expires_at' => (string) $serial->warranty_expires_at,
                'is_warranty_active' => $serial->isWarrantyActive(),
                'status' => $serial->status,
            ];
        })->toArray();

        return [
            'customer_found' => true,
            'name' => $contact->name,
            'phone' => $contact->phone,
            'email' => $contact->email,
            'address' => $contact->address,
            'ceb_account_no' => $contact->ceb_account_no,
            'outstanding_balance_lkr' => (float) $contact->balance,
            'projects' => $projects,
            'invoices' => $invoices,
            'installed_equipment_serials' => $equipmentSerials,
        ];
    }

    /**
     * Gemini Function Calling tool definitions schema.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getToolDeclarations(): array
    {
        return [
            [
                'name' => 'get_project_profit_analysis',
                'description' => 'Fetches revenue, itemized material bills, installation labor, agent commission, office overhead, and net owner profit for specific projects or customers (e.g. Fernando, Gamini, or quote VHE/2026/00179).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'search_query' => [
                            'type' => 'STRING',
                            'description' => 'The customer name, invoice number, quote number, or CEB account number to search for (e.g. "Fernando", "Gamini", "VHE/2026/00179").',
                        ],
                    ],
                    'required' => ['search_query'],
                ],
            ],
            [
                'name' => 'get_financial_kpi_summary',
                'description' => 'Fetches total invoiced sales, cash payments collected, total expenses, retained margin, and net profit for the entire company.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'period' => [
                            'type' => 'STRING',
                            'description' => 'The time period to analyze. Options: "all_time", "this_month", "last_month", "this_quarter", "this_year". Defaults to "all_time".',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'get_unpaid_invoices',
                'description' => 'Lists pending and overdue invoices, customer names, contact phone numbers, and balance dues across the company.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'get_inventory_status',
                'description' => 'Lists stock on hand, serial numbers, cost prices, and valuations for Panels, Inverters, Batteries, and BOS accessories.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'category' => [
                            'type' => 'STRING',
                            'description' => 'Optional filter for inventory item name or category (e.g. "Panel", "Inverter", "Battery", "BOS").',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'get_customer_overview',
                'description' => 'Returns customer profile, solar project quotations, agreement dates, kW capacities, invoices, and installed equipment warranty terms.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'customer_name' => [
                            'type' => 'STRING',
                            'description' => 'The customer name or phone or CEB account number to search for (e.g. "Fernando", "Gamini").',
                        ],
                    ],
                    'required' => ['customer_name'],
                ],
            ],
        ];
    }
}
