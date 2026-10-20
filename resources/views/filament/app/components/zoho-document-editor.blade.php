@php
    $tenant = \Filament\Facades\Filament::getTenant();
    $companyId = $tenant?->id;
    $contacts = \App\Models\Contact::where('company_id', $companyId)->orderBy('name')->get();
    $products = \App\Models\Product::where('company_id', $companyId)->orderBy('name')->get();
    $categories = \App\Models\Category::where('company_id', $companyId)->orderBy('name')->get();
    $salesAgents = \App\Models\SalesAgent::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

    $docType = $this->documentType ?? 'quotation';
    $isQuote = $docType === 'quotation';
    $isEdit = isset($this->record) && $this->record instanceof \Illuminate\Database\Eloquent\Model && $this->record->exists;
    $isOwnerOrAdmin = auth()->user()?->isTenantOwnerOrAdmin() ?? false;
@endphp

<div class="zoho-invoice-container">
    <style>
        .zoho-invoice-container {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #ffffff;
            color: #1e293b;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: 100%;
        }

        /* Top Gray Header Zone */
        .zoho-top-zone {
            background-color: #f9fafb;
            border-bottom: 1px solid #edf2f7;
            padding: 20px 28px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .zoho-customer-row {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .zoho-field-label-red {
            width: 160px;
            font-size: 13px;
            font-weight: 600;
            color: #e53e3e;
            display: flex;
            align-items: center;
            gap: 2px;
            flex-shrink: 0;
        }

        .zoho-customer-select-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            max-width: 600px;
        }

        .zoho-select, .zoho-input, .zoho-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            color: #1e293b;
            outline: none;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .zoho-select:focus, .zoho-input:focus, .zoho-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .zoho-btn-search {
            background-color: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background-color 0.15s;
        }
        .zoho-btn-search:hover {
            background-color: #2563eb;
        }

        .zoho-currency-badge {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 6px 14px;
            border-radius: 9999px;
            margin-left: auto;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Document Meta Section */
        .zoho-meta-section {
            padding: 24px 28px;
            border-bottom: 1px solid #f1f5f9;
        }

        .zoho-form-row {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        .zoho-form-row:last-child {
            margin-bottom: 0;
        }

        .zoho-row-label {
            width: 160px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            flex-shrink: 0;
        }
        .zoho-row-label.required {
            color: #e53e3e;
        }

        .zoho-row-controls {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .zoho-control-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .zoho-sublabel {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
        }

        /* Solar Specs Collapsible Toggle Bar */
        .zoho-solar-specs-toggle-bar {
            margin: 20px 28px;
            background: linear-gradient(90deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1px dashed #86efac;
            border-radius: 8px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }
        .zoho-solar-specs-toggle-bar:hover {
            border-color: #22c55e;
            background: #f0fdf4;
        }
        .zoho-solar-specs-content {
            margin: 0 28px 24px;
            padding: 16px 20px;
            background-color: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 16px;
        }

        /* Item Table Styles */
        .zoho-table-card {
            margin: 0 28px 24px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
        }

        .zoho-table-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafc;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .zoho-table-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .zoho-table-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .zoho-grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .zoho-grid-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .zoho-grid-table th.right, .zoho-grid-table td.right {
            text-align: right;
        }

        .zoho-grid-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .zoho-grid-table tr:hover {
            background-color: #fafbfc;
        }

        .zoho-row-delete-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            font-size: 16px;
            line-height: 1;
            transition: color 0.15s, background-color 0.15s;
        }
        .zoho-row-delete-btn:hover {
            color: #ef4444;
            background-color: #fee2e2;
        }

        /* Pill Buttons Below Table */
        .zoho-table-footer-buttons {
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: #ffffff;
            border-top: 1px solid #f1f5f9;
        }

        .zoho-pill-btn-primary {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .zoho-pill-btn-primary:hover {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .zoho-pill-btn-secondary {
            background-color: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .zoho-pill-btn-secondary:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        /* Bottom Split Section */
        .zoho-bottom-split {
            display: flex;
            padding: 24px 28px;
            gap: 32px;
            border-top: 1px solid #edf2f7;
            background-color: #ffffff;
        }

        .zoho-bottom-left {
            width: 55%;
        }

        .zoho-bottom-right {
            width: 45%;
        }

        .zoho-summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
        }

        .zoho-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: #475569;
        }

        .zoho-grand-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0 6px 0;
            border-top: 2px solid #e2e8f0;
            margin-top: 8px;
        }

        .zoho-grand-total-label {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .zoho-grand-total-value {
            font-size: 20px;
            font-weight: 800;
            color: #047857;
        }

        /* Bank & Payment Box */
        .zoho-bank-details-card {
            margin-top: 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 12px;
            line-height: 1.6;
            color: #334155;
        }

        /* Bottom Action Bar */
        .zoho-action-bar {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .zoho-btn-save {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 9px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s;
        }
        .zoho-btn-save:hover {
            background-color: #1d4ed8;
        }

        .zoho-btn-secondary {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 9px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s;
            text-decoration: none;
            display: inline-block;
        }
        .zoho-btn-secondary:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        /* Bulk Modal Overlay */
        .zoho-modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            backdrop-filter: blur(2px);
        }

        .zoho-modal-container {
            background: #ffffff;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .zoho-modal-header {
            padding: 16px 24px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .zoho-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
        }

        .zoho-modal-footer {
            padding: 14px 24px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Dark Mode SaaS Palette */
        .dark .zoho-invoice-container {
            background: #0f172a;
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-top-zone {
            background-color: #111827;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-select, .dark .zoho-input, .dark .zoho-textarea {
            background-color: #1e293b;
            border-color: rgba(255, 255, 255, 0.12);
            color: #f8fafc;
        }
        .dark .zoho-select:focus, .dark .zoho-input:focus, .dark .zoho-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .dark .zoho-meta-section {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-row-label {
            color: #94a3b8;
        }
        .dark .zoho-solar-specs-toggle-bar {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .dark .zoho-solar-specs-content {
            background-color: #111827;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-table-card {
            background-color: #0f172a;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-table-header-bar {
            background-color: #111827;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-table-title {
            color: #f8fafc;
        }
        .dark .zoho-grid-table th {
            background-color: #111827;
            color: #94a3b8;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-grid-table td {
            border-bottom-color: rgba(255, 255, 255, 0.06);
            color: #f8fafc;
        }
        .dark .zoho-grid-table tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }
        .dark .zoho-table-footer-buttons {
            background-color: #111827;
            border-top-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-pill-btn-primary {
            background-color: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }
        .dark .zoho-pill-btn-secondary {
            background-color: #1e293b;
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.12);
        }
        .dark .zoho-bottom-split {
            background-color: #0f172a;
            border-top-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-summary-box {
            background-color: #111827;
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-summary-row {
            color: #94a3b8;
        }
        .dark .zoho-grand-total-row {
            border-top-color: rgba(255, 255, 255, 0.1);
        }
        .dark .zoho-grand-total-label {
            color: #f8fafc;
        }
        .dark .zoho-bank-details-card {
            background-color: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }
        .dark .zoho-action-bar {
            background-color: #111827;
            border-top-color: rgba(255, 255, 255, 0.08);
        }
        .dark .zoho-btn-secondary {
            background-color: #1e293b;
            border-color: rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
        }
        .dark .zoho-modal-container {
            background-color: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }
        .dark .zoho-modal-header, .dark .zoho-modal-footer {
            background-color: #111827;
            border-color: rgba(255, 255, 255, 0.08);
        }
    </style>

    {{-- Top Subtle Gray Zone for Customer Selection --}}
    <div class="zoho-top-zone">
        <div class="zoho-customer-row">
            <label class="zoho-field-label-red">
                Customer Name*
            </label>
            <div class="zoho-customer-select-wrap">
                <select wire:model.live="data.contact_id" class="zoho-select" required>
                    <option value="">Select or search a customer...</option>
                    @foreach ($contacts as $contact)
                        <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->phone ?? $contact->email }})</option>
                    @endforeach
                </select>

                <button
                    type="button"
                    wire:click="openQuickCustomerModal"
                    class="zoho-pill-btn-primary"
                    style="padding: 6px 12px; font-size: 12px; white-space: nowrap; display: flex; align-items: center; gap: 4px;"
                    title="Quick Add New Customer"
                >
                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    + Quick Add Customer
                </button>
            </div>

            <div class="zoho-currency-badge">
                <svg style="width: 0.875rem; height: 0.875rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                </svg>
                LKR — Sri Lankan Rupee
            </div>
        </div>
    </div>

    {{-- Horizontal Form Rows: Number, Dates, Terms --}}
    <div class="zoho-meta-section">
        {{-- Row 1: Document Number & Status --}}
        <div class="zoho-form-row">
            <label class="zoho-row-label required">
                {{ $isQuote ? 'Quote#*' : 'Invoice#*' }}
            </label>
            <div class="zoho-row-controls">
                <input
                    type="text"
                    wire:model="data.{{ $isQuote ? 'quote_number' : 'invoice_number' }}"
                    class="zoho-input"
                    style="max-width: 280px;"
                    required
                />

                <div class="zoho-control-group">
                    <span class="zoho-sublabel">Status:</span>
                    <select wire:model="data.status" class="zoho-select" style="width: 140px; padding: 6px 10px;">
                        @if ($isQuote)
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="invoiced">Invoiced</option>
                        @else
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partially Paid</option>
                            <option value="paid">Fully Paid</option>
                            <option value="cancelled">Cancelled</option>
                        @endif
                    </select>
                </div>

                @if (! $isQuote)
                    <div class="zoho-control-group">
                        <span class="zoho-sublabel">Invoice Type:</span>
                        <select wire:model="data.type" class="zoho-select" style="width: 180px; padding: 6px 10px;">
                            <option value="standard">Standard Tax Invoice</option>
                            <option value="pos">POS Counter Sale</option>
                            <option value="milestone">Milestone Solar Billing</option>
                        </select>
                    </div>
                @endif
            </div>
        </div>

        {{-- Row 2: Dates and Payment Terms --}}
        <div class="zoho-form-row">
            <label class="zoho-row-label required">
                {{ $isQuote ? 'Quote Date*' : 'Invoice Date*' }}
            </label>
            <div class="zoho-row-controls">
                <input
                    type="date"
                    wire:model="data.{{ $isQuote ? 'quote_date' : 'invoice_date' }}"
                    class="zoho-input"
                    style="width: 160px;"
                    required
                />

                <div class="zoho-control-group">
                    <span class="zoho-sublabel">Terms:</span>
                    <select wire:model="data.terms" class="zoho-select" style="width: 160px;">
                        <option value="Due on Receipt">Due on Receipt</option>
                        <option value="Net 15">Net 15</option>
                        <option value="Net 30">Net 30</option>
                        <option value="Valid for 07 days">Valid for 07 days</option>
                        <option value="Valid for 30 days">Valid for 30 days</option>
                    </select>
                </div>

                <div class="zoho-control-group">
                    <span class="zoho-sublabel">{{ $isQuote ? 'Valid Until:' : 'Due Date:' }}</span>
                    <input
                        type="date"
                        wire:model="data.{{ $isQuote ? 'valid_until' : 'due_date' }}"
                        class="zoho-input"
                        style="width: 160px;"
                    />
                </div>
            </div>
        </div>

        {{-- Row 3: Sales Agent / Referrer & Commission --}}
        <div class="zoho-form-row">
            <label class="zoho-row-label">
                Sales Agent / Referrer
            </label>
            <div class="zoho-row-controls" style="flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 8px; flex: 1; min-width: 280px; max-width: 440px;">
                    <select wire:model.live="data.sales_agent_id" class="zoho-select">
                        <option value="">-- No Introducer / Direct Sale --</option>
                        @foreach ($salesAgents as $agent)
                            <option value="{{ $agent->id }}">
                                {{ $agent->name }} ({{ $agent->agent_code }}) — {{ $agent->default_commission_type === 'percentage' ? "{$agent->default_commission_value}%" : 'Rs. '.number_format($agent->default_commission_value, 2) }}
                            </option>
                        @endforeach
                    </select>

                    <button
                        type="button"
                        wire:click="openQuickAgentModal"
                        class="zoho-pill-btn-secondary"
                        style="padding: 6px 10px; font-size: 11px; white-space: nowrap; border-color: #0d9488; color: #0f766e; background: #f0fdfa; display: flex; align-items: center; gap: 4px;"
                        title="Quick Register Sales Agent"
                    >
                        <span>+ Quick Add Agent</span>
                    </button>
                </div>

                <div class="zoho-control-group">
                    <span class="zoho-sublabel">Commission (Rs.):</span>
                    <input
                        type="number"
                        step="any"
                        min="0"
                        wire:model.live.debounce.300ms="data.commission_amount"
                        class="zoho-input right"
                        style="width: 140px; font-weight: 700; color: #d97706;"
                        placeholder="0.00"
                    />
                </div>

                <div class="zoho-control-group">
                    <span class="zoho-sublabel">Commission Status:</span>
                    <select wire:model="data.commission_status" class="zoho-select" style="width: 120px;">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Row 4: PDF & Print Mode Selector --}}
        <div class="zoho-form-row" style="background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 14px;">
            <label class="zoho-row-label" style="color: #0f172a; font-weight: 700; width: 170px;">
                📄 PDF & Print Mode
            </label>
            <div class="zoho-row-controls" style="display: flex; gap: 20px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: #1e293b;">
                    <input
                        type="radio"
                        wire:model.live="data.price_display_mode"
                        value="itemized"
                        style="accent-color: #3b82f6; width: 16px; height: 16px;"
                    />
                    <span>Standard Itemized (Show Individual Unit Prices)</span>
                </label>

                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: #047857;">
                    <input
                        type="radio"
                        wire:model.live="data.price_display_mode"
                        value="package_total_only"
                        style="accent-color: #059669; width: 16px; height: 16px;"
                    />
                    <span>Customer Package View (Hide Unit Prices — Show Total & Discount Only)</span>
                </label>
            </div>
        </div>
    </div>

    @php
        $solarSpecsEnabled = (bool) ($tenant?->settings['enable_solar_specs'] ?? ($tenant?->settings['show_solar_specs'] ?? true));
    @endphp

    @if ($solarSpecsEnabled)
        {{-- Sleek Collapsible Bar for Solar System Specifications --}}
        <div wire:click="toggleSolarSpecs" class="zoho-solar-specs-toggle-bar">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 13px; color: #15803d;">
                <span>☀️</span>
                <span>Solar System Engineering Specifications (kWp, Phase, Roof, Inverter, Panel)</span>
                <span style="font-size: 11px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-weight: 500;">
                    {{ $this->data['system_kw'] ?? 5 }} kWp • {{ ucfirst($this->data['phase_type'] ?? 'single') }} Phase
                </span>
            </div>
            <div style="color: #166534; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                <span>{{ $this->showSolarSpecs ? 'Collapse' : 'Expand Specifications' }}</span>
                <svg style="width: 1rem; height: 1rem; transform: {{ $this->showSolarSpecs ? 'rotate(180deg)' : 'rotate(0)' }}; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        @if ($this->showSolarSpecs)
            <div class="zoho-solar-specs-content">
                <div style="grid-column: span 3;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block;">System Capacity (kWp)</label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" wire:model.live.debounce.300ms="data.system_kw" class="zoho-input" placeholder="5.00" />
                        <span style="position: absolute; right: 10px; top: 8px; font-size: 11px; color: #94a3b8; font-weight: 600;">kWp</span>
                    </div>
                </div>

                <div style="grid-column: span 3;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block;">Grid Connection Phase</label>
                    <select wire:model="data.phase_type" class="zoho-select">
                        <option value="single">Single Phase (230V)</option>
                        <option value="three">Three Phase (400V)</option>
                    </select>
                </div>

                <div style="grid-column: span 6;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block;">Roof Structure Mounting</label>
                    <select wire:model="data.roof_type" class="zoho-select">
                        <option value="Tile Roof">Tile Roof (Standard Sri Lankan Clay Tile)</option>
                        <option value="Asbestos Sheet">Asbestos / Corrugated Sheet</option>
                        <option value="Zinc-Alum / Corrugated">Zinc-Alum / Standing Seam Roof</option>
                        <option value="Flat Concrete Slab">Flat Concrete Slab (Ballasted/Elevated)</option>
                        <option value="Ground Mount">Ground Mount Structure</option>
                    </select>
                </div>

                <div style="grid-column: span 6;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block;">Inverter Brand & Model</label>
                    <input type="text" wire:model="data.inverter_brand" class="zoho-input" placeholder="e.g. GoodWe GW5000D-NS / Huawei SUN2000" />
                </div>

                <div style="grid-column: span 6;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; display: block;">PV Panel Brand & Rating</label>
                    <input type="text" wire:model="data.panel_brand" class="zoho-input" placeholder="e.g. METEOR 610W-630W N-type TopCon Bifacial" />
                </div>
            </div>
        @endif
    @endif

    {{-- True Zoho "Item Table" HTML Grid --}}
    <div class="zoho-table-card">
        <div class="zoho-table-header-bar">
            <div class="zoho-table-title">
                <span>Item Table</span>
                <span style="font-size: 11px; background: #e2e8f0; color: #475569; padding: 2px 7px; border-radius: 9999px;">
                    {{ count($this->data['items'] ?? []) }} items
                </span>
            </div>

            <div class="zoho-table-actions">
                <button type="button" wire:click="openBulkModal" class="zoho-pill-btn-secondary">
                    <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Scan / Multi-Add Items
                </button>

                <div x-data="{ open: false }" style="position: relative;">
                    <button type="button" @click="open = !open" class="zoho-pill-btn-primary">
                        <span>⚡ Solar Package Preset</span>
                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" style="position: absolute; right: 0; top: 100%; margin-top: 6px; width: 280px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; padding: 6px 0;" x-cloak>
                        <a href="javascript:void(0)" wire:click="applySolarPreset('5kw'); open = false;" style="display: block; padding: 8px 16px; font-size: 12px; color: #1e293b; text-decoration: none; hover: background: #f8fafc;">
                            <div style="font-weight: 600;">5kW On-Grid Solar Package</div>
                            <div style="font-size: 11px; color: #64748b;">GoodWe 5kW + 8x Meteor 620W Panels</div>
                        </a>
                        <a href="javascript:void(0)" wire:click="applySolarPreset('10kw'); open = false;" style="display: block; padding: 8px 16px; font-size: 12px; color: #1e293b; text-decoration: none; border-top: 1px solid #f1f5f9;">
                            <div style="font-weight: 600;">10kW Three-Phase Solar Package</div>
                            <div style="font-size: 11px; color: #64748b;">GoodWe 10kW + 16x Meteor Panels</div>
                        </a>
                        <a href="javascript:void(0)" wire:click="applySolarPreset('15kw'); open = false;" style="display: block; padding: 8px 16px; font-size: 12px; color: #1e293b; text-decoration: none; border-top: 1px solid #f1f5f9;">
                            <div style="font-weight: 600;">15kW Commercial Solar Package</div>
                            <div style="font-size: 11px; color: #64748b;">Huawei 15kW + 24x Panels + Switchgear</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <table class="zoho-grid-table">
            <thead>
                <tr>
                    <th style="width: 40%;">ITEM DETAILS</th>
                    <th style="width: 12%;" class="right">QUANTITY</th>
                    <th style="width: 15%;" class="right">RATE (Rs.)</th>
                    <th style="width: 12%;" class="right">DISCOUNT (%)</th>
                    <th style="width: 11%;" class="right">TAX (Rs.)</th>
                    <th style="width: 10%;" class="right">AMOUNT (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->data['items'] ?? [] as $index => $item)
                    <tr wire:key="item-row-{{ $index }}">
                        {{-- ITEM DETAILS: Product Dropdown + Description Textarea --}}
                        {{-- ITEM DETAILS: Product Dropdown or Custom Free-Text --}}
                        <td>
                            <select
                                wire:change="updateItemProduct({{ $index }}, $event.target.value)"
                                class="zoho-select"
                                style="margin-bottom: 6px; font-weight: 500;"
                            >
                                <option value="">-- Custom Item / Free-Text Entry --</option>
                                @foreach ($products as $prod)
                                    <option value="{{ $prod->id }}" @selected(($item['product_id'] ?? null) == $prod->id)>
                                        {{ $prod->name }} — Rs. {{ number_format($prod->unit_price, 2) }}
                                    </option>
                                @endforeach
                            </select>

                            <textarea
                                wire:model="data.items.{{ $index }}.description"
                                rows="2"
                                class="zoho-textarea"
                                placeholder="Item name, specifications or custom ad-hoc description..."
                                style="font-size: 12px;"
                            ></textarea>

                            <div style="margin-top: 6px; display: flex; flex-direction: column; gap: 4px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span style="font-size: 10.5px; font-weight: 600; color: #64748b; white-space: nowrap;">Warranty:</span>
                                    <input
                                        type="text"
                                        wire:model="data.items.{{ $index }}.warranty_text"
                                        class="zoho-input"
                                        style="font-size: 11px; padding: 3px 8px; height: 26px;"
                                        placeholder="e.g. 10 Years Warranty / 30 Years Performance"
                                    />
                                </div>

                                <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b; cursor: pointer;">
                                    <input
                                        type="checkbox"
                                        wire:model="data.items.{{ $index }}.hide_on_customer_pdf"
                                        style="accent-color: #f59e0b; width: 13px; height: 13px;"
                                    />
                                    <span>Hide row on Customer Package PDF (Internal cost row included in package total)</span>
                                </label>
                            </div>

                            @if ($isOwnerOrAdmin)
                                <div style="font-size: 10.5px; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                    <span style="font-weight: 600;">🔒 Unit Cost: Rs.</span>
                                    <input
                                        type="number"
                                        step="any"
                                        min="0"
                                        wire:model.live.debounce.300ms="data.items.{{ $index }}.cost_price"
                                        class="zoho-input"
                                        style="width: 90px; padding: 2px 6px; font-size: 11px; text-align: right; height: 24px;"
                                        title="Internal Unit Cost (Owner/Admin Only - Never visible on customer PDFs)"
                                    />
                                    <span style="font-size: 9.5px; color: #94a3b8;">(Owner only)</span>
                                </div>
                            @endif
                        </td>

                        {{-- QUANTITY: Right aligned numeric input --}}
                        <td class="right">
                            <input
                                type="number"
                                step="any"
                                min="0.01"
                                wire:model.live.debounce.300ms="data.items.{{ $index }}.quantity"
                                class="zoho-input right"
                                style="font-weight: 600; text-align: right;"
                            />
                        </td>

                        {{-- RATE: Right aligned numeric input --}}
                        <td class="right">
                            <input
                                type="number"
                                step="any"
                                wire:model.live.debounce.300ms="data.items.{{ $index }}.unit_price"
                                class="zoho-input right"
                                style="font-weight: 600; text-align: right;"
                            />
                        </td>

                        {{-- DISCOUNT: Numeric input with % badge --}}
                        <td class="right">
                            <div style="position: relative;">
                                <input
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="100"
                                    wire:model.live.debounce.300ms="data.items.{{ $index }}.discount_rate"
                                    class="zoho-input right"
                                    style="padding-right: 22px; text-align: right;"
                                />
                                <span style="position: absolute; right: 8px; top: 8px; font-size: 11px; color: #94a3b8; font-weight: 700;">%</span>
                            </div>
                        </td>

                        {{-- TAX: Numeric tax input --}}
                        <td class="right">
                            <input
                                type="number"
                                step="any"
                                wire:model.live.debounce.300ms="data.items.{{ $index }}.tax"
                                class="zoho-input right"
                                style="text-align: right;"
                            />
                        </td>

                        {{-- AMOUNT: Calculated Line Total + Row Delete button --}}
                        <td class="right" style="position: relative;">
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                <span style="font-weight: 700; font-size: 14px; color: #0f172a;">
                                    {{ number_format($item['subtotal'] ?? 0, 2) }}
                                </span>
                                <button
                                    type="button"
                                    wire:click="removeItemRow({{ $index }})"
                                    class="zoho-row-delete-btn"
                                    title="Delete line item"
                                >
                                    &times;
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Below Table Action Buttons --}}
        <div class="zoho-table-footer-buttons">
            <button type="button" wire:click="addItemRow" class="zoho-pill-btn-primary">
                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                + Add New Row
            </button>

            <button type="button" wire:click="openQuickItemModal" class="zoho-pill-btn-secondary" style="border-color: #3b82f6; color: #2563eb; background: #eff6ff;">
                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                + Quick Add New Item
            </button>

            <button type="button" wire:click="openBulkModal" class="zoho-pill-btn-secondary">
                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                + Add Items in Bulk
            </button>
        </div>
    </div>

    {{-- Zoho-Style Bottom Split Section: Left (55%) vs Right (45%) --}}
    <div class="zoho-bottom-split">
        {{-- Left Column (55%): Customer Notes + Terms & Bank Details --}}
        <div class="zoho-bottom-left">
            <div style="margin-bottom: 20px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">
                    Customer Notes
                </label>
                <textarea
                    wire:model="data.customer_notes"
                    rows="3"
                    class="zoho-textarea"
                    placeholder="Will be displayed on the document..."
                ></textarea>
                <span style="font-size: 11px; color: #94a3b8;">Will be displayed on the customer document</span>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: block;">
                    Terms & Conditions & Bank Wire Details
                </label>
                <textarea
                    wire:model="data.terms"
                    rows="4"
                    class="zoho-textarea"
                    placeholder="Enter document warranty & payment milestone terms..."
                ></textarea>

                @php
                    $editorBankAccounts = $tenant?->getBankAccountsList() ?? [];
                @endphp
                @if (!empty($editorBankAccounts))
                    <div class="zoho-bank-details-card">
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;" class="dark:text-slate-100">
                            <span>🏦</span> Verified Company Remittance Accounts:
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px;">
                            @foreach ($editorBankAccounts as $acc)
                                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 2.5px solid #047857; border-radius: 6px; padding: 8px 10px; font-size: 11px; line-height: 1.45;" class="dark:!bg-slate-800 dark:!border-slate-700">
                                    <div style="font-weight: 800; color: #0f172a;" class="dark:text-slate-100">{{ $acc['bank'] ?? 'Bank' }}</div>
                                    @if (!empty($acc['account_name']))
                                        <div style="color: #64748b; font-size: 10px;" class="dark:text-slate-400">{{ $acc['account_name'] }}</div>
                                    @endif
                                    @if (!empty($acc['account_no']))
                                        <div style="font-family: monospace; font-size: 12px; font-weight: 800; color: #0f172a; margin: 2px 0;" class="dark:text-emerald-400">{{ $acc['account_no'] }}</div>
                                    @endif
                                    @if (!empty($acc['branch']))
                                        <div style="color: #64748b; font-size: 10px;" class="dark:text-slate-400">Branch: <strong>{{ $acc['branch'] }}</strong></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column (45%): Summary Box --}}
        <div class="zoho-bottom-right">
            <div class="zoho-summary-box">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 13px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.05em;">Financial Summary</span>
                    <button
                        type="button"
                        wire:click="toggleTotalSummary"
                        style="background: transparent; border: none; color: #2563eb; font-size: 12px; font-weight: 600; cursor: pointer;"
                    >
                        {{ $this->showTotalSummary ? 'Hide Summary ^' : 'Show Total Summary v' }}
                    </button>
                </div>

                @if ($this->showTotalSummary)
                    <div class="zoho-summary-row">
                        <span>Subtotal</span>
                        <span style="font-weight: 600; color: #1e293b;">
                            Rs. {{ number_format($this->data['subtotal'] ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="zoho-summary-row">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span>Discount</span>
                            <div style="display: inline-flex; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; font-size: 11px;">
                                <button
                                    type="button"
                                    wire:click="setDiscountType('fixed')"
                                    style="padding: 2px 7px; border: none; font-weight: 700; cursor: pointer; {{ $this->discountType === 'fixed' ? 'background: #047857; color: #ffffff;' : 'background: #f1f5f9; color: #64748b;' }}"
                                >
                                    LKR
                                </button>
                                <button
                                    type="button"
                                    wire:click="setDiscountType('percentage')"
                                    style="padding: 2px 7px; border: none; font-weight: 700; cursor: pointer; {{ $this->discountType === 'percentage' ? 'background: #047857; color: #ffffff;' : 'background: #f1f5f9; color: #64748b;' }}"
                                >
                                    %
                                </button>
                            </div>
                        </div>
                        <div style="max-width: 150px; text-align: right;">
                            @if ($this->discountType === 'percentage')
                                <div style="position: relative;">
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        max="100"
                                        wire:model.live.debounce.300ms="discountPercent"
                                        class="zoho-input right"
                                        style="padding: 4px 22px 4px 8px; font-size: 12px; font-weight: 600; text-align: right;"
                                        placeholder="0"
                                    />
                                    <span style="position: absolute; right: 6px; top: 6px; font-size: 11px; color: #94a3b8; font-weight: 700;">%</span>
                                </div>
                                <div style="font-size: 10px; color: #047857; margin-top: 2px; font-weight: 600;">
                                    - Rs. {{ number_format($this->data['discount'] ?? 0, 2) }}
                                </div>
                            @else
                                <input
                                    type="number"
                                    step="any"
                                    wire:model.live.debounce.300ms="data.discount"
                                    class="zoho-input right"
                                    style="padding: 4px 8px; font-size: 12px; font-weight: 600; text-align: right;"
                                />
                            @endif
                        </div>
                    </div>

                    <div class="zoho-summary-row" style="align-items: flex-start;">
                        <div>
                            <div>Tax / Applicable VAT (Rs.)</div>
                            <div style="display: flex; gap: 4px; margin-top: 4px;">
                                <button
                                    type="button"
                                    wire:click="applyTaxPreset(0, 'Tax Exempt')"
                                    style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 10px; padding: 2px 6px; color: #475569; font-weight: 600; cursor: pointer;"
                                    title="0% Tax Exempt"
                                >
                                    Exempt
                                </button>
                                <button
                                    type="button"
                                    wire:click="applyTaxPreset(18, 'VAT 18%')"
                                    style="background: #eff6ff; border: 1px solid #93c5fd; border-radius: 4px; font-size: 10px; padding: 2px 6px; color: #1d4ed8; font-weight: 700; cursor: pointer;"
                                    title="Sri Lanka 18% VAT"
                                >
                                    18% VAT
                                </button>
                                <button
                                    type="button"
                                    wire:click="applyTaxPreset(2.5, 'SSCL 2.5%')"
                                    style="background: #fefce8; border: 1px solid #fde047; border-radius: 4px; font-size: 10px; padding: 2px 6px; color: #854d0e; font-weight: 700; cursor: pointer;"
                                    title="Sri Lanka 2.5% SSCL"
                                >
                                    2.5% SSCL
                                </button>
                            </div>
                        </div>
                        <div style="max-width: 150px;">
                            <input
                                type="number"
                                step="any"
                                wire:model.live.debounce.300ms="data.tax"
                                class="zoho-input right"
                                style="padding: 4px 8px; font-size: 12px; font-weight: 600; text-align: right;"
                            />
                        </div>
                    </div>
                @endif

                {{-- Zoho Grand Total Display --}}
                <div class="zoho-grand-total-row">
                    <span class="zoho-grand-total-label">Total ( LKR )</span>
                    <span class="zoho-grand-total-value">
                        Rs. {{ number_format($this->data['grand_total'] ?? 0, 2) }}
                    </span>
                </div>

                {{-- If Invoice, show Paid Amount and Balance Due --}}
                @if (! $isQuote)
                    <div style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed #cbd5e1;">
                        <div class="zoho-summary-row">
                            <span style="font-weight: 600; color: #047857;">Amount Paid (Rs.)</span>
                            <div style="max-width: 140px;">
                                <input
                                    type="number"
                                    step="any"
                                    wire:model.live.debounce.300ms="data.paid_amount"
                                    class="zoho-input right"
                                    style="padding: 4px 8px; font-size: 12px; font-weight: 700; color: #047857; text-align: right;"
                                />
                            </div>
                        </div>

                        <div class="zoho-summary-row" style="margin-top: 6px;">
                            <span style="font-weight: 700; color: #dc2626; font-size: 14px;">Balance Due (Rs.)</span>
                            <span style="font-size: 16px; font-weight: 800; color: #dc2626;">
                                Rs. {{ number_format($this->data['due_amount'] ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Internal Profit Analysis (Strictly Owner/Admin Only) --}}
                @if ($isOwnerOrAdmin)
                    @php
                        $cost = $this->totalCost;
                        $grossProfit = $this->grossProfit;
                        $commission = $this->agentCommission;
                        $profit = $this->netProfit;
                        $margin = $this->profitMarginPercentage;
                    @endphp
                    <div style="margin-top: 16px; background: rgba(16, 185, 129, 0.08); border: 1px dashed rgba(16, 185, 129, 0.4); border-radius: 8px; padding: 12px 14px;">
                        <div style="font-size: 10.5px; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;">
                            <span>🔒 Owner Profit Analysis</span>
                            <span style="font-size: 9px; padding: 1px 6px; border-radius: 4px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 700;" class="dark:bg-emerald-950/80 dark:text-emerald-300">Owner Only</span>
                        </div>
                        <div style="font-size: 12px; color: #475569; display: flex; flex-direction: column; gap: 6px;" class="dark:text-slate-300">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Product Cost (COGS):</span>
                                <strong style="color: #334155;" class="dark:text-slate-200">Rs. {{ number_format($cost, 2) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Gross Profit:</span>
                                <strong style="color: {{ $grossProfit >= 0 ? '#059669' : '#dc2626' }};">Rs. {{ number_format($grossProfit, 2) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Agent Commission:</span>
                                <strong style="color: #d97706;">Rs. {{ number_format($commission, 2) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-top: 1px dashed rgba(16, 185, 129, 0.3); padding-top: 5px; margin-top: 2px;">
                                <span>Net Owner Profit:</span>
                                <strong style="font-size: 13px; color: {{ $profit >= 0 ? '#059669' : '#dc2626' }};">Rs. {{ number_format($profit, 2) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Net Profit Margin:</span>
                                <strong style="font-size: 12px; color: {{ $margin >= 20 ? '#059669' : ($margin >= 0 ? '#d97706' : '#dc2626') }};">{{ number_format($margin, 1) }}%</strong>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Action Buttons --}}
    <div class="zoho-action-bar">
        @if ($isEdit)
            <button type="button" wire:click="save" class="zoho-btn-save">
                Save Changes
            </button>
        @else
            <button type="button" wire:click="create" class="zoho-btn-save">
                {{ $isQuote ? 'Save as Draft Quote' : 'Save Invoice' }}
            </button>
            <button type="button" wire:click="create" class="zoho-btn-save" style="background-color: #059669;">
                Save & Send
            </button>
        @endif

        <a href="{{ $this->getResource()::getUrl('index') }}" class="zoho-btn-secondary">
            Cancel
        </a>
    </div>

    {{-- Multi-Select Bulk Items Modal --}}
    @if ($this->showBulkModal)
        <div class="zoho-modal-overlay">
            <div class="zoho-modal-container">
                <div class="zoho-modal-header">
                    <div style="font-weight: 700; font-size: 15px; color: #0f172a;">
                        Multi-Select Solar Equipment & Inventory
                    </div>
                    <button type="button" wire:click="closeBulkModal" style="background: transparent; border: none; font-size: 20px; color: #64748b; cursor: pointer;">
                        &times;
                    </button>
                </div>

                <div class="zoho-modal-body">
                    <div style="margin-bottom: 14px;">
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="bulkSearch"
                            placeholder="Search equipment by name, brand, SKU or capacity..."
                            class="zoho-input"
                        />
                    </div>

                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <th style="padding: 8px 12px; text-align: left; width: 40px;">Select</th>
                                <th style="padding: 8px 12px; text-align: left;">Product / Equipment Name</th>
                                <th style="padding: 8px 12px; text-align: right; width: 90px;">In Stock</th>
                                <th style="padding: 8px 12px; text-align: right; width: 130px;">Unit Price (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $filteredProducts = $products;
                                if (! empty($this->bulkSearch)) {
                                    $search = strtolower($this->bulkSearch);
                                    $filteredProducts = $filteredProducts->filter(function ($p) use ($search) {
                                        return str_contains(strtolower($p->name), $search) || str_contains(strtolower($p->sku ?? ''), $search);
                                    });
                                }
                            @endphp

                            @forelse ($filteredProducts as $prod)
                                @php $isSelected = in_array($prod->id, $this->selectedBulkProducts); @endphp
                                <tr
                                    wire:click="toggleBulkProduct({{ $prod->id }})"
                                    style="border-bottom: 1px solid #f1f5f9; cursor: pointer; background-color: {{ $isSelected ? '#eff6ff' : 'transparent' }};"
                                >
                                    <td style="padding: 8px 12px;">
                                        <input
                                            type="checkbox"
                                            value="{{ $prod->id }}"
                                            @checked($isSelected)
                                            style="cursor: pointer;"
                                        />
                                    </td>
                                    <td style="padding: 8px 12px;">
                                        <div style="font-weight: 600; color: #1e293b;">{{ $prod->name }}</div>
                                        <div style="font-size: 11px; color: #64748b;">SKU: {{ $prod->sku ?? 'N/A' }}</div>
                                    </td>
                                    <td style="padding: 8px 12px; text-align: right; font-weight: 600; color: {{ $prod->stock_qty > 0 ? '#059669' : '#dc2626' }};">
                                        {{ $prod->stock_qty }}
                                    </td>
                                    <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #0f172a;">
                                        Rs. {{ number_format($prod->unit_price, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 24px; text-align: center; color: #94a3b8;">
                                        No products matched your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="zoho-modal-footer">
                    <button type="button" wire:click="closeBulkModal" class="zoho-btn-secondary" style="padding: 6px 14px; font-size: 12px;">
                        Cancel
                    </button>
                    <button type="button" wire:click="addSelectedBulkProducts" class="zoho-btn-save" style="padding: 6px 16px; font-size: 12px;">
                        Add Selected ({{ count($this->selectedBulkProducts) }} items)
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Add Customer Modal --}}
    @if ($this->showQuickCustomerModal)
        <div class="zoho-modal-overlay">
            <div class="zoho-modal-container" style="max-width: 500px;">
                <div class="zoho-modal-header">
                    <div style="font-weight: 700; font-size: 15px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>👤</span> Quick Add New Customer
                    </div>
                    <button type="button" wire:click="closeQuickCustomerModal" style="background: transparent; border: none; font-size: 20px; color: #64748b; cursor: pointer;">
                        &times;
                    </button>
                </div>

                <div class="zoho-modal-body" style="padding: 20px;">
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Customer / Business Name*</label>
                        <input type="text" wire:model="quickCustomerName" class="zoho-input" placeholder="e.g. Kasun Perera / Apex Engineering" required />
                        @error('quickCustomerName') <span style="font-size: 11px; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Phone Number</label>
                            <input type="text" wire:model="quickCustomerPhone" class="zoho-input" placeholder="e.g. 0771234567" />
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Email Address</label>
                            <input type="email" wire:model="quickCustomerEmail" class="zoho-input" placeholder="customer@domain.lk" />
                        </div>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Installation / Billing Address</label>
                        <textarea wire:model="quickCustomerAddress" rows="2" class="zoho-textarea" placeholder="No. 45, Galle Road, Colombo..."></textarea>
                    </div>
                </div>

                <div class="zoho-modal-footer">
                    <button type="button" wire:click="closeQuickCustomerModal" class="zoho-btn-secondary" style="padding: 6px 14px; font-size: 12px;">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveQuickCustomer" class="zoho-btn-save" style="padding: 6px 18px; font-size: 12px;">
                        Save & Select Customer
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Add Item Modal --}}
    @if ($this->showQuickItemModal)
        <div class="zoho-modal-overlay">
            <div class="zoho-modal-container" style="max-width: 500px;">
                <div class="zoho-modal-header">
                    <div style="font-weight: 700; font-size: 15px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>📦</span> Quick Add Product / Service
                    </div>
                    <button type="button" wire:click="closeQuickItemModal" style="background: transparent; border: none; font-size: 20px; color: #64748b; cursor: pointer;">
                        &times;
                    </button>
                </div>

                <div class="zoho-modal-body" style="padding: 20px;">
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Item / Service Name*</label>
                        <input type="text" wire:model="quickItemName" class="zoho-input" placeholder="e.g. 550W Bifacial Solar Panel / Labor Charge" required />
                        @error('quickItemName') <span style="font-size: 11px; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">SKU / Code</label>
                            <input type="text" wire:model="quickItemSku" class="zoho-input" placeholder="e.g. SP-550W" />
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Unit Selling Price (Rs.)*</label>
                            <input type="number" step="any" wire:model="quickItemPrice" class="zoho-input" placeholder="0.00" required />
                            @error('quickItemPrice') <span style="font-size: 11px; color: #dc2626;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Item Type</label>
                            <select wire:model="quickItemType" class="zoho-select">
                                <option value="product">Physical Product / Hardware</option>
                                <option value="service">Service / Installation / Consulting</option>
                            </select>
                        </div>
                        <div style="display: flex; align-items: center; padding-top: 18px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: #334155; cursor: pointer;">
                                <input type="checkbox" wire:model="quickItemTrackInventory" style="cursor: pointer;" />
                                Track Stock Inventory
                            </label>
                        </div>
                    </div>
                </div>

                <div class="zoho-modal-footer">
                    <button type="button" wire:click="closeQuickItemModal" class="zoho-btn-secondary" style="padding: 6px 14px; font-size: 12px;">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveQuickItem" class="zoho-btn-save" style="padding: 6px 18px; font-size: 12px;">
                        Save & Add to Document
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Add Sales Agent Modal --}}
    @if ($this->showQuickAgentModal)
        <div class="zoho-modal-overlay">
            <div class="zoho-modal-container" style="max-width: 500px;">
                <div class="zoho-modal-header">
                    <div style="font-weight: 700; font-size: 15px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>🤝</span> Quick Register Sales Agent / Referrer
                    </div>
                    <button type="button" wire:click="closeQuickAgentModal" style="background: transparent; border: none; font-size: 20px; color: #64748b; cursor: pointer;">
                        &times;
                    </button>
                </div>

                <div class="zoho-modal-body" style="padding: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Agent Code</label>
                            <input type="text" wire:model="quickAgentCode" class="zoho-input" placeholder="e.g. AGT-005" />
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Phone / Mobile</label>
                            <input type="text" wire:model="quickAgentPhone" class="zoho-input" placeholder="e.g. 077 123 4567" />
                        </div>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Agent Full Name*</label>
                        <input type="text" wire:model="quickAgentName" class="zoho-input" placeholder="e.g. Ruwan Jayasinghe" required />
                        @error('quickAgentName') <span style="font-size: 11px; color: #dc2626;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Commission Model</label>
                            <select wire:model="quickAgentCommissionType" class="zoho-select">
                                <option value="fixed">Fixed LKR Amount</option>
                                <option value="percentage">Percentage (%) of Total</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;">Default Value (Rs. / %)</label>
                            <input type="number" step="any" wire:model="quickAgentCommissionValue" class="zoho-input" placeholder="0.00" />
                        </div>
                    </div>
                </div>

                <div class="zoho-modal-footer">
                    <button type="button" wire:click="closeQuickAgentModal" class="zoho-btn-secondary" style="padding: 6px 14px; font-size: 12px;">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveQuickAgent" class="zoho-btn-save" style="padding: 6px 18px; font-size: 12px;">
                        Save & Assign Agent
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
