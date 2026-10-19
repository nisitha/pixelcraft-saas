@php
    $tenant = \Filament\Facades\Filament::getTenant();
    $company = $tenant;
    $record = $this->record;
    $docType = $this->documentType ?? 'quotation';
    $isQuote = $docType === 'quotation';
    $primaryColor = $company->settings['primary_color'] ?? '#047857';
    $tableHeaderColor = $company->settings['table_header_color'] ?? '#047857';
    $textColor = $company->settings['text_color'] ?? '#ffffff';
    $showSolarSpecs = $company->settings['show_solar_specs'] ?? true;
    $showCebAccount = $company->settings['show_ceb_account'] ?? true;
    $showMilestones = $company->settings['show_milestones'] ?? true;
@endphp

<div class="zoho-split-screen-wrapper">
    <style>
        .zoho-split-screen-wrapper {
            display: flex;
            gap: 20px;
            margin-top: -12px;
            min-height: calc(100vh - 120px);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        /* Left Master Pane (32%) */
        .zoho-left-pane {
            width: 32%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dark .zoho-left-pane {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5);
        }

        .zoho-left-header {
            padding: 16px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .dark .zoho-left-header {
            background-color: #0f172a;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .zoho-left-search {
            width: 100%;
            padding: 8px 12px;
            font-size: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            background-color: #ffffff;
        }
        .dark .zoho-left-search {
            background-color: #1e293b;
            color: #f8fafc;
            border-color: #334155;
        }
        .zoho-left-search:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }

        .zoho-left-filters {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            overflow-x: auto;
        }

        .zoho-filter-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            white-space: nowrap;
        }
        .dark .zoho-filter-btn {
            background: #1e293b;
            color: #cbd5e1;
            border-color: #334155;
        }
        .zoho-filter-btn.active {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #ffffff;
        }

        .zoho-records-list {
            flex: 1;
            overflow-y: auto;
            max-height: calc(100vh - 240px);
        }

        .zoho-record-item {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
        }
        .dark .zoho-record-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #f8fafc;
        }
        .zoho-record-item:hover {
            background-color: #f8fafc;
        }
        .dark .zoho-record-item:hover {
            background-color: rgba(255, 255, 255, 0.04);
        }
        .zoho-record-item.active {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
        }
        .dark .zoho-record-item.active {
            background-color: rgba(59, 130, 246, 0.15);
        }

        /* Right Detail Pane (68%) */
        .zoho-right-pane {
            width: 68%;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Top Action Bar */
        .zoho-detail-action-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dark .zoho-detail-action-bar {
            background: #0f172a;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .zoho-action-btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .action-btn-default {
            background-color: #f8fafc;
            color: #334155;
            border-color: #cbd5e1;
        }
        .action-btn-default:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .dark .action-btn-default {
            background-color: #1e293b;
            color: #f8fafc;
            border-color: #334155;
        }
        .dark .action-btn-default:hover {
            background-color: #334155;
        }

        .action-btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .action-btn-primary:hover {
            background-color: #1d4ed8;
        }

        .action-btn-whatsapp {
            background-color: #16a34a;
            color: #ffffff;
        }
        .action-btn-whatsapp:hover {
            background-color: #15803d;
        }

        .action-btn-success {
            background-color: #059669;
            color: #ffffff;
        }
        .action-btn-success:hover {
            background-color: #047857;
        }

        .action-btn-locked {
            background-color: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        /* What's Next Workflow Banner */
        .zoho-workflow-banner {
            background: linear-gradient(90deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Live A4 Paper Preview Container */
        .zoho-a4-container {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 32px 16px;
            overflow-y: auto;
            max-height: calc(100vh - 220px);
            display: flex;
            justify-content: center;
        }
        .dark .zoho-a4-container {
            background-color: #0b0f19;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .a4-paper {
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            background: #ffffff !important;
            color: #1e293b !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0 !important;
            border-radius: 4px;
            padding: 40px 44px;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Diagonal Ribbon */
        .corner-ribbon {
            position: absolute;
            top: 24px;
            right: -42px;
            width: 160px;
            text-align: center;
            transform: rotate(45deg);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 6px 0;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .ribbon-draft { background-color: #64748b; }
        .ribbon-sent { background-color: #2563eb; }
        .ribbon-paid, .ribbon-accepted { background-color: #059669; }
        .ribbon-unpaid { background-color: #e11d48; }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            backdrop-filter: blur(2px);
        }
        .modal-card {
            background: #ffffff;
            border-radius: 12px;
            width: 90%;
            max-width: 520px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    {{-- Left Pane: Master Scrollable Document List (32%) --}}
    <div class="zoho-left-pane">
        <div class="zoho-left-header">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.05em;">
                    All {{ $isQuote ? 'Quotations' : 'Invoices' }}
                </span>
                <a href="{{ $this->getResource()::getUrl('create') }}" class="action-btn action-btn-primary" style="padding: 4px 10px; font-size: 11px;">
                    + New
                </a>
            </div>

            <input
                type="text"
                wire:model.live.debounce.250ms="search"
                class="zoho-left-search"
                placeholder="Search {{ $isQuote ? 'quote' : 'invoice' }}# or client..."
            />

            <div class="zoho-left-filters">
                <button type="button" wire:click="$set('statusFilter', 'all')" class="zoho-filter-btn {{ $this->statusFilter === 'all' ? 'active' : '' }}">
                    All
                </button>
                <button type="button" wire:click="$set('statusFilter', 'draft')" class="zoho-filter-btn {{ $this->statusFilter === 'draft' ? 'active' : '' }}">
                    Draft
                </button>
                <button type="button" wire:click="$set('statusFilter', 'sent')" class="zoho-filter-btn {{ $this->statusFilter === 'sent' ? 'active' : '' }}">
                    Sent
                </button>
                @if ($isQuote)
                    <button type="button" wire:click="$set('statusFilter', 'accepted')" class="zoho-filter-btn {{ $this->statusFilter === 'accepted' ? 'active' : '' }}">
                        Accepted
                    </button>
                @else
                    <button type="button" wire:click="$set('statusFilter', 'paid')" class="zoho-filter-btn {{ $this->statusFilter === 'paid' ? 'active' : '' }}">
                        Paid
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'unpaid')" class="zoho-filter-btn {{ $this->statusFilter === 'unpaid' ? 'active' : '' }}">
                        Unpaid
                    </button>
                @endif
            </div>
        </div>

        <div class="zoho-records-list">
            @forelse ($this->recordsList as $item)
                @php
                    $isSelected = $record && $record->id === $item->id;
                    $itemStatus = strtolower($item->status);
                    $num = $isQuote ? $item->quote_number : $item->invoice_number;
                    $dt = $isQuote ? $item->quote_date : $item->invoice_date;
                @endphp
                <div
                    wire:click="selectRecord({{ $item->id }})"
                    class="zoho-record-item {{ $isSelected ? 'active' : '' }}"
                >
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                        <span style="font-weight: 700; font-size: 13px; color: #0f172a;">
                            {{ $item->contact?->name ?? 'Valued Customer' }}
                        </span>
                        <span style="font-weight: 800; font-size: 13px; color: #047857;">
                            Rs. {{ number_format($item->grand_total, 2) }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #64748b;">
                        <span>{{ $num }} • {{ $dt?->format('d M Y') ?? 'N/A' }}</span>
                        <span style="padding: 2px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase; font-size: 10px;
                            {{ $itemStatus === 'paid' || $itemStatus === 'accepted' ? 'background: #dcfce7; color: #166534;' : '' }}
                            {{ $itemStatus === 'sent' ? 'background: #dbeafe; color: #1e40af;' : '' }}
                            {{ $itemStatus === 'draft' ? 'background: #f1f5f9; color: #475569;' : '' }}
                            {{ $itemStatus === 'unpaid' ? 'background: #fee2e2; color: #991b1b;' : '' }}
                        ">
                            {{ $itemStatus }}
                        </span>
                    </div>
                </div>
            @empty
                <div style="padding: 32px; text-align: center; color: #94a3b8; font-size: 12px;">
                    No documents found.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Right Pane: Detail View with Top Action Bar + What's Next + Live A4 Paper (68%) --}}
    <div class="zoho-right-pane">
        @if ($record)
            @php
                $status = strtolower($record->status ?? 'draft');
                $isSent = (bool) ($record->is_sent ?? false) || $status === 'sent';
                $ribbonClass = match ($status) {
                    'sent' => 'ribbon-sent',
                    'paid', 'accepted', 'invoiced' => 'ribbon-paid',
                    'unpaid' => 'ribbon-unpaid',
                    default => 'ribbon-draft',
                };
                $ribbonText = strtoupper($status === 'invoiced' ? 'ACCEPTED' : $status);
                $docNum = $isQuote ? $record->quote_number : $record->invoice_number;
                $docDate = $isQuote ? ($record->quote_date?->format('d M Y') ?? now()->format('d M Y')) : ($record->invoice_date?->format('d M Y') ?? now()->format('d M Y'));
                $dueDate = $isQuote ? ($record->valid_until?->format('d M Y') ?? '30 Days') : ($record->due_date?->format('d M Y') ?? '14 Days');
            @endphp

            {{-- Top Action Bar --}}
            <div class="zoho-detail-action-bar">
                <div class="zoho-action-btn-group">
                    {{-- Edit Locking Logic: If sent, show Unlock; else Edit --}}
                    @if ($isSent)
                        <button type="button" wire:click="unlockForEditing" class="action-btn action-btn-locked" title="Document is locked because it was marked as sent to customer. Click to unlock and edit.">
                            🔒 Unlock & Edit
                        </button>
                    @else
                        <a href="{{ $this->getResource()::getUrl('edit', ['record' => $record->id]) }}" class="action-btn action-btn-default">
                            ✏️ Edit
                        </a>
                    @endif

                    <button type="button" wire:click="openEmailModal" class="action-btn action-btn-primary">
                        ✉️ Send Email
                    </button>

                    <a href="{{ $this->whatsAppUrl }}" target="_blank" class="action-btn action-btn-whatsapp">
                        💬 WhatsApp Share
                    </a>

                    <button
                        type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText('{{ $this->publicShareUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
                        class="action-btn action-btn-default"
                    >
                        <span x-show="!copied">🔗 Copy Link</span>
                        <span x-show="copied" style="color: #059669;" x-cloak>✓ Copied!</span>
                    </button>

                    <button type="button" wire:click="downloadPdf" class="action-btn action-btn-default">
                        📄 PDF / Print
                    </button>

                    <button type="button" wire:click="downloadDeliveryNote" class="action-btn action-btn-default" title="Print Delivery Note / Gate Pass without prices">
                        🚚 Delivery Note
                    </button>

                    @if (! $isQuote && $record && $record->payments()->count() > 0)
                        <button type="button" wire:click="downloadReceiptVoucher" class="action-btn action-btn-default" title="Download Official Payment Receipt Voucher">
                            🧾 Receipt Voucher
                        </button>
                    @endif

                    <button type="button" wire:click="duplicateRecord" class="action-btn action-btn-default" title="1-Click Clone / Duplicate">
                        📑 Duplicate
                    </button>
                </div>

                <div class="zoho-action-btn-group">
                    @if ($isQuote)
                        <button type="button" wire:click="convertToInvoice" class="action-btn action-btn-success">
                            🔄 Convert to Invoice
                        </button>
                    @else
                        @if ($status !== 'paid')
                            <button type="button" wire:click="openPaymentModal" class="action-btn action-btn-success">
                                💰 Record Payment
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            {{-- "WHAT'S NEXT?" Workflow Banner --}}
            @if (! $isSent || $status === 'draft')
                <div class="zoho-workflow-banner">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">⚡</span>
                        <div>
                            <div style="font-weight: 800; font-size: 13px; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em;">
                                WHAT'S NEXT?
                            </div>
                            <div style="font-size: 12px; color: #78350f;">
                                Send this {{ $docType }} to your customer via WhatsApp / Email, or mark it as Sent to lock editing.
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" wire:click="openEmailModal" class="action-btn action-btn-primary" style="padding: 6px 12px; font-size: 12px;">
                            Send {{ ucfirst($docType) }}
                        </button>
                        <button type="button" wire:click="markAsSent" class="action-btn action-btn-default" style="padding: 6px 12px; font-size: 12px;">
                            Mark As Sent
                        </button>
                    </div>
                </div>
            @endif

            {{-- Live A4 Paper Container --}}
            <div class="zoho-a4-container">
                <div class="a4-paper">
                    <div class="corner-ribbon {{ $ribbonClass }}">{{ $ribbonText }}</div>

                    <!-- Document Header -->
                    <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px; margin-bottom: 24px;">
                        <div>
                            @if ($company->getFilamentAvatarUrl())
                                <img src="{{ $company->getFilamentAvatarUrl() }}" style="max-height: 52px; max-width: 200px; margin-bottom: 6px; object-fit: contain;" alt="{{ $company->name }}">
                            @endif
                            <h2 style="font-size: 20px; font-weight: 800; color: {{ $primaryColor }}; margin-bottom: 4px;">{{ $company->name }}</h2>
                            <div style="font-size: 11px; color: #475569; line-height: 1.5;">
                                @if ($company->address) {{ $company->address }}<br> @endif
                                @if ($company->phone) Tel: {{ $company->phone }} | @endif
                                @if ($company->email) Email: {{ $company->email }}<br> @endif
                                @if ($company->tax_number) Tax/VAT/TIN: <strong>{{ $company->tax_number }}</strong> @endif
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div style="display: inline-block; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 12px; font-weight: 800; padding: 5px 12px; border-radius: 4px; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 8px;">
                                {{ $isQuote ? 'SOLAR PV QUOTATION' : 'TAX INVOICE' }}
                            </div>
                            <div style="font-size: 12px; color: #334155; line-height: 1.6;">
                                <div><strong>{{ $isQuote ? 'Quote No:' : 'Invoice No:' }}</strong> {{ $docNum }}</div>
                                <div><strong>Date:</strong> {{ $docDate }}</div>
                                <div><strong>{{ $isQuote ? 'Valid Until:' : 'Due Date:' }}</strong> {{ $dueDate }}</div>
                                <div><strong>Status:</strong> <span style="font-weight: 700; color: {{ $primaryColor }}; text-transform: uppercase;">{{ $status }}</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Client & Utility Cards -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 14px; font-size: 11px; line-height: 1.5;">
                            <div style="font-size: 11px; font-weight: 700; color: {{ $primaryColor }}; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;">
                                Bill To / Customer Information
                            </div>
                            <div style="font-weight: 700; font-size: 12px; color: #0f172a;">{{ $record->contact?->name ?? 'Valued Customer' }}</div>
                            @if ($record->contact?->address) <div>{{ $record->contact->address }}</div> @endif
                            @if ($record->contact?->phone) <div>Phone: {{ $record->contact->phone }}</div> @endif
                            @if ($record->contact?->email) <div>Email: {{ $record->contact->email }}</div> @endif
                        </div>

                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 14px; font-size: 11px; line-height: 1.5;">
                            <div style="font-size: 11px; font-weight: 700; color: {{ $primaryColor }}; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;">
                                Grid Connection & Engineering
                            </div>
                            <div>Utility: Ceylon Electricity Board (CEB) / LECO</div>
                            @if ($showCebAccount && !empty($record->contact?->ceb_account_no))
                                <div style="margin-top: 4px; font-weight: 700; color: #92400e; background: #fef3c7; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                    CEB Acc No: {{ $record->contact->ceb_account_no }}
                                </div>
                            @endif
                            @if ($showSolarSpecs && !empty($record->system_kw))
                                <div style="margin-top: 4px;">System Capacity: <strong>{{ $record->system_kw }} kWp</strong> ({{ ucfirst($record->phase_type ?? 'Single') }} Phase)</div>
                            @endif
                        </div>
                    </div>

                    <!-- Items Grid -->
                    <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 18px;">
                        <thead>
                            <tr style="background-color: {{ $tableHeaderColor }}; color: {{ $textColor }};">
                                <th style="padding: 8px 10px; text-align: left; width: 5%;">#</th>
                                <th style="padding: 8px 10px; text-align: left; width: 50%;">Item & Specifications</th>
                                <th style="padding: 8px 10px; text-align: right; width: 12%;">Qty</th>
                                <th style="padding: 8px 10px; text-align: right; width: 15%;">Rate (LKR)</th>
                                <th style="padding: 8px 10px; text-align: right; width: 18%;">Amount (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($record->items as $idx => $item)
                                <tr style="border-bottom: 1px solid #f1f5f9; background-color: {{ $idx % 2 === 1 ? '#f8fafc' : 'transparent' }};">
                                    <td style="padding: 8px 10px;">{{ $idx + 1 }}</td>
                                    <td style="padding: 8px 10px;">
                                        <div style="font-weight: 600; color: #0f172a;">{{ $item->product?->name ?? $item->description }}</div>
                                        @if (!empty($item->description) && $item->description !== ($item->product?->name ?? ''))
                                            <div style="font-size: 10px; color: #64748b;">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 8px 10px; text-align: right;">{{ number_format($item->quantity, 2) }}</td>
                                    <td style="padding: 8px 10px; text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                                    <td style="padding: 8px 10px; text-align: right; font-weight: 700; color: #0f172a;">{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Bottom Summary & Remittance -->
                    <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 20px;">
                        <div>
                            @php
                                $bankAccounts = $company->getBankAccountsList();
                            @endphp
                            @if (!empty($bankAccounts))
                                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; font-size: 10px; line-height: 1.5;">
                                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px; font-size: 11px;">🏦 Electronic Bank Wire Remittance:</div>
                                    <div style="display: grid; grid-template-columns: {{ count($bankAccounts) > 1 ? '1fr 1fr' : '1fr' }}; gap: 8px;">
                                        @foreach($bankAccounts as $acc)
                                            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px;">
                                                <div style="font-weight: 700; color: {{ $primaryColor }}; font-size: 10.5px; border-bottom: 1px solid #f1f5f9; padding-bottom: 2px; margin-bottom: 3px;">
                                                    🏛️ {{ $acc['bank'] ?? 'Bank Account' }}
                                                </div>
                                                <div style="font-size: 9.5px; color: #475569; line-height: 1.45;">
                                                    <div><span style="color: #64748b;">Acc Name:</span> <strong>{{ $acc['account_name'] ?? $company->name }}</strong></div>
                                                    <div><span style="color: #64748b;">Acc No:</span> <code style="font-weight: 800; font-family: monospace; font-size: 10.5px; color: {{ $primaryColor }};">{{ $acc['account_no'] ?? '-' }}</code></div>
                                                    @if(!empty($acc['branch']))
                                                        <div><span style="color: #64748b;">Branch:</span> {{ $acc['branch'] }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif(!empty($company->settings['bank_details']))
                                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; font-size: 10px; line-height: 1.5;">
                                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">🏦 Electronic Bank Wire Remittance:</div>
                                    {!! nl2br(e($company->settings['bank_details'])) !!}
                                </div>
                            @endif

                            @if (!empty($record->terms))
                                <div style="margin-top: 10px; font-size: 10px; color: #475569; line-height: 1.5;">
                                    <div style="font-weight: 700; color: #0f172a;">Terms & Conditions:</div>
                                    {!! nl2br(e($record->terms)) !!}
                                </div>
                            @endif
                        </div>

                        <div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                <tr>
                                    <td style="padding: 4px 6px;">Subtotal:</td>
                                    <td style="padding: 4px 6px; text-align: right; font-weight: 600;">Rs. {{ number_format($record->subtotal, 2) }}</td>
                                </tr>
                                @if ((float) $record->discount > 0)
                                    <tr>
                                        <td style="padding: 4px 6px;">Promotional Discount:</td>
                                        <td style="padding: 4px 6px; text-align: right; color: #dc2626;">- Rs. {{ number_format($record->discount, 2) }}</td>
                                    </tr>
                                @endif
                                @if ((float) $record->tax > 0)
                                    <tr>
                                        <td style="padding: 4px 6px;">Tax / VAT:</td>
                                        <td style="padding: 4px 6px; text-align: right;">Rs. {{ number_format($record->tax, 2) }}</td>
                                    </tr>
                                @endif
                                <tr style="background-color: {{ $primaryColor }}; color: #ffffff; font-weight: 800;">
                                    <td style="padding: 8px 10px; border-radius: 4px 0 0 4px;">Total (LKR):</td>
                                    <td style="padding: 8px 10px; text-align: right; border-radius: 0 4px 4px 0;">Rs. {{ number_format($record->grand_total, 2) }}</td>
                                </tr>
                                @if (! $isQuote)
                                    <tr>
                                        <td style="padding: 4px 6px; font-weight: 600; color: #059669;">Amount Paid:</td>
                                        <td style="padding: 4px 6px; text-align: right; font-weight: 700; color: #059669;">Rs. {{ number_format($record->paid_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 6px; font-weight: 700; color: #dc2626;">Balance Due:</td>
                                        <td style="padding: 4px 6px; text-align: right; font-weight: 800; color: #dc2626;">Rs. {{ number_format($record->due_amount, 2) }}</td>
                                    </tr>
                                @endif
                            </table>

                            @if (auth()->user()?->isTenantOwnerOrAdmin())
                                <div style="margin-top: 14px; background: linear-gradient(135deg, #064e3b 0%, #022c22 100%); border: 1px solid #059669; border-radius: 6px; padding: 10px 12px; color: #ffffff;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 4px; margin-bottom: 6px;">
                                        <div style="font-size: 10px; font-weight: 800; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.05em;">
                                            🔒 Internal Profit Analysis (Owner Only)
                                        </div>
                                        <span style="font-size: 9px; background: rgba(16,185,129,0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 1px 4px; border-radius: 3px; font-weight: 700;">CONFIDENTIAL</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; font-size: 10px;">
                                        <div>
                                            <div style="color: #cbd5e1; font-size: 9px;">Cost (COGS):</div>
                                            <div style="font-weight: 700; font-size: 10.5px; color: #f8fafc;">Rs. {{ number_format($record->total_cost, 2) }}</div>
                                        </div>
                                        <div>
                                            <div style="color: #cbd5e1; font-size: 9px;">Gross Profit:</div>
                                            <div style="font-weight: 700; font-size: 10.5px; color: #a7f3d0;">Rs. {{ number_format($record->gross_profit, 2) }}</div>
                                        </div>
                                        <div>
                                            <div style="color: #cbd5e1; font-size: 9px;">Commission:</div>
                                            <div style="font-weight: 700; font-size: 10.5px; color: #fde047;">Rs. {{ number_format((float) ($record->commission_amount ?? 0), 2) }}</div>
                                        </div>
                                        <div>
                                            <div style="color: #cbd5e1; font-size: 9px;">Net Owner Profit:</div>
                                            <div style="font-weight: 800; font-size: 11px; color: {{ $record->net_profit >= 0 ? '#34d399' : '#f87171' }};">
                                                Rs. {{ number_format($record->net_profit, 2) }} ({{ $record->profit_margin_percentage }}%)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($company->settings['signature_path']))
                                <div style="margin-top: 20px; text-align: right;">
                                    <img src="{{ asset('storage/' . $company->settings['signature_path']) }}" style="max-height: 48px; max-width: 140px; display: inline-block;" alt="Signature">
                                    <div style="font-size: 10px; font-weight: 600; color: #475569; border-top: 1px solid #cbd5e1; padding-top: 4px; display: inline-block; min-width: 140px;">
                                        Authorized Signatory
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 60px; text-align: center; color: #94a3b8;">
                Select a document from the left list to view live A4 preview.
            </div>
        @endif
    </div>

    {{-- Send Email Modal --}}
    @if ($this->showEmailModal)
        <div class="modal-overlay">
            <div class="modal-card">
                <div style="padding: 16px 20px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-weight: 700; font-size: 14px; color: #0f172a;">Send {{ ucfirst($docType) }} to Customer</span>
                    <button type="button" wire:click="closeEmailModal" style="background: transparent; border: none; font-size: 18px; color: #64748b; cursor: pointer;">&times;</button>
                </div>

                <div style="padding: 20px;">
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Recipient Email*</label>
                        <input type="email" wire:model="emailRecipient" class="zoho-left-search" placeholder="customer@example.com" required>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Subject Line*</label>
                        <input type="text" wire:model="emailSubject" class="zoho-left-search" required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Message Body</label>
                        <textarea wire:model="emailMessage" rows="5" class="zoho-left-search" style="font-size: 12px;"></textarea>
                    </div>
                </div>

                <div style="padding: 12px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="closeEmailModal" class="action-btn action-btn-default">Cancel</button>
                    <button type="button" wire:click="sendEmail" class="action-btn action-btn-primary">Send & Mark as Sent</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Record Payment Modal (for Invoices) --}}
    @if ($this->showPaymentModal)
        <div class="modal-overlay">
            <div class="modal-card">
                <div style="padding: 16px 20px; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-weight: 700; font-size: 14px; color: #0f172a;">Record Payment for Invoice #{{ $record->invoice_number }}</span>
                    <button type="button" wire:click="closePaymentModal" style="background: transparent; border: none; font-size: 18px; color: #64748b; cursor: pointer;">&times;</button>
                </div>

                <div style="padding: 20px;">
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Payment Amount (Rs.)*</label>
                        <input type="number" step="any" wire:model="paymentAmount" class="zoho-left-search" required>
                        <span style="font-size: 11px; color: #64748b;">Current Balance Due: Rs. {{ number_format($record->due_amount, 2) }}</span>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Payment Mode / Method</label>
                        <select wire:model="paymentMethod" class="zoho-left-search">
                            <option value="bank_transfer">Bank Wire / Electronic Transfer</option>
                            <option value="cash">Cash Counter</option>
                            <option value="cheque">Cheque Deposit</option>
                            <option value="credit_card">Card Payment</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Transaction Reference / Slip No</label>
                        <input type="text" wire:model="paymentReference" class="zoho-left-search" placeholder="e.g. BOC-WIRE-99281">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Notes</label>
                        <input type="text" wire:model="paymentNotes" class="zoho-left-search" placeholder="Optional payment note">
                    </div>
                </div>

                <div style="padding: 12px 20px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                    <button type="button" wire:click="closePaymentModal" class="action-btn action-btn-default">Cancel</button>
                    <button type="button" wire:click="submitPayment" class="action-btn action-btn-success">Record Payment</button>
                </div>
            </div>
        </div>
    @endif
</div>
