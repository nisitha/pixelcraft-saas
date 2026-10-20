<div class="linear-pipeline-card">
    <style>
        .linear-pipeline-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 10px;
            padding: 18px 20px;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: 100%;
        }

        html.dark .linear-pipeline-card {
            background: #121316;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }

        .linear-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .linear-card-title {
            font-size: 11px;
            font-weight: 500;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .linear-conversion-wrap {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f4f4f5;
        }

        html.dark .linear-conversion-wrap {
            border-bottom-color: rgba(255, 255, 255, 0.06);
        }

        .linear-conversion-metric-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        .linear-conversion-val {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #09090b;
        }

        html.dark .linear-conversion-val {
            color: #f4f4f5;
        }

        .linear-conversion-sub {
            font-size: 11.5px;
            color: #71717a;
            font-weight: 450;
        }

        .linear-progress-track {
            width: 100%;
            height: 6px;
            background: #f4f4f5;
            border-radius: 9999px;
            overflow: hidden;
        }

        html.dark .linear-progress-track {
            background: rgba(255, 255, 255, 0.06);
        }

        .linear-progress-fill {
            height: 100%;
            border-radius: 9999px;
            background: #10b981;
            transition: width 0.4s ease;
        }

        /* 4-Item Pipeline Micro Counters */
        .linear-stage-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .linear-stage-box {
            padding: 6px 8px;
            border-radius: 6px;
            background: #fafafa;
            border: 1px solid #e4e4e7;
            text-align: center;
        }

        html.dark .linear-stage-box {
            background: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.06);
        }

        .linear-stage-count {
            font-size: 13px;
            font-weight: 600;
            color: #18181b;
        }

        html.dark .linear-stage-count {
            color: #f4f4f5;
        }

        .linear-stage-lbl {
            font-size: 10px;
            font-weight: 500;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        /* Recent Activity Status List */
        .linear-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 500;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .linear-list-link {
            font-size: 11px;
            font-weight: 500;
            color: #71717a;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .linear-list-link:hover {
            color: #09090b;
        }

        html.dark .linear-list-link:hover {
            color: #f4f4f5;
        }

        .linear-quote-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .linear-quote-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            border-radius: 6px;
            background: #fafafa;
            border: 1px solid #f4f4f5;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        html.dark .linear-quote-row {
            background: rgba(255, 255, 255, 0.02);
            border-color: rgba(255, 255, 255, 0.04);
        }

        .linear-quote-row:hover {
            border-color: #d4d4d8;
            background: #ffffff;
        }

        html.dark .linear-quote-row:hover {
            border-color: rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.04);
        }

        .linear-quote-left {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .linear-status-dot {
            width: 7px;
            height: 7px;
            min-width: 7px;
            border-radius: 50%;
        }

        .linear-dot-draft { background-color: #71717a; }
        .linear-dot-sent { background-color: #3b82f6; }
        .linear-dot-accepted { background-color: #10b981; }
        .linear-dot-invoiced { background-color: #8b5cf6; }

        .linear-quote-customer {
            font-size: 12px;
            font-weight: 500;
            color: #18181b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        html.dark .linear-quote-customer {
            color: #f4f4f5;
        }

        .linear-quote-kw {
            font-size: 11px;
            font-weight: 450;
            color: #71717a;
            background: rgba(0, 0, 0, 0.04);
            padding: 1px 6px;
            border-radius: 4px;
        }

        html.dark .linear-quote-kw {
            background: rgba(255, 255, 255, 0.05);
            color: #a1a1aa;
        }

        .linear-quote-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .linear-quote-number {
            font-size: 11px;
            color: #71717a;
            font-family: ui-monospace, monospace;
        }

        .linear-quote-amount {
            font-size: 11.5px;
            font-weight: 600;
            color: #09090b;
        }

        html.dark .linear-quote-amount {
            color: #f4f4f5;
        }
    </style>

    <!-- Header & Conversion Progress Bar -->
    <div class="linear-conversion-wrap">
        <div class="linear-card-header">
            <span class="linear-card-title">Quotation Pipeline & Conversion</span>
            <span class="linear-conversion-sub">{{ $invoicedCount }} / {{ $totalQuotesCount }} Invoiced</span>
        </div>

        <div class="linear-conversion-metric-row">
            <span class="linear-conversion-val">{{ $conversionRate }}%</span>
            <span class="linear-conversion-sub">Conversion Rate</span>
        </div>

        <div class="linear-progress-track">
            <div class="linear-progress-fill" style="width: {{ min(100, max(0, $conversionRate)) }}%;"></div>
        </div>
    </div>

    <!-- Pipeline Stages Row -->
    <div class="linear-stage-row">
        <div class="linear-stage-box">
            <div class="linear-stage-count">{{ $pipeline['draft']['count'] }}</div>
            <div class="linear-stage-lbl">Draft</div>
        </div>
        <div class="linear-stage-box">
            <div class="linear-stage-count">{{ $pipeline['sent']['count'] }}</div>
            <div class="linear-stage-lbl">Sent</div>
        </div>
        <div class="linear-stage-box">
            <div class="linear-stage-count">{{ $pipeline['accepted']['count'] }}</div>
            <div class="linear-stage-lbl">Accepted</div>
        </div>
        <div class="linear-stage-box">
            <div class="linear-stage-count">{{ $pipeline['invoiced']['count'] }}</div>
            <div class="linear-stage-lbl">Invoiced</div>
        </div>
    </div>

    <!-- Recent Status List -->
    <div>
        <div class="linear-list-header" style="margin-bottom: 8px;">
            <span>Recent Pipeline Activity</span>
            <a href="{{ $quotationsUrl }}" class="linear-list-link">View All &rarr;</a>
        </div>

        @if($recentQuotations->isNotEmpty())
            <div class="linear-quote-list">
                @foreach($recentQuotations as $quote)
                    @php
                        $status = strtolower($quote->status ?? 'draft');
                        $dotClass = match($status) {
                            'sent' => 'linear-dot-sent',
                            'accepted' => 'linear-dot-accepted',
                            'invoiced' => 'linear-dot-invoiced',
                            default => 'linear-dot-draft',
                        };
                        $customerName = $quote->contact?->name ?? 'Direct Customer';
                        $kwText = $quote->system_kw ? number_format($quote->system_kw, 1) . ' kWp' : 'PV';
                    @endphp
                    <a href="{{ \App\Filament\App\Resources\Quotations\QuotationResource::getUrl('edit', ['record' => $quote->id]) }}" class="linear-quote-row" title="{{ $customerName }} - {{ $quote->quote_number }}">
                        <div class="linear-quote-left">
                            <span class="linear-status-dot {{ $dotClass }}" title="{{ ucfirst($status) }}"></span>
                            <span class="linear-quote-customer">{{ $customerName }}</span>
                            <span class="linear-quote-kw">{{ $kwText }}</span>
                        </div>
                        <div class="linear-quote-right">
                            <span class="linear-quote-number">{{ $quote->quote_number }}</span>
                            <span class="linear-quote-amount">Rs. {{ number_format($quote->grand_total, 0) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="font-size: 12px; color: #71717a; padding: 12px 0; text-align: center;">
                No quotations recorded yet.
            </div>
        @endif
    </div>
</div>
