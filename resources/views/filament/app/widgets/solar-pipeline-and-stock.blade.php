<div class="able-pipeline-card">
    <style>
        .able-pipeline-card {
            background: #ffffff;
            border: 1px solid #e8ebee;
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 2px 12px rgba(33, 40, 50, 0.05);
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: 100%;
        }

        html.dark .able-pipeline-card {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }

        .able-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .able-card-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #5b6b79;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        html.dark .able-card-title {
            color: #94a3b8;
        }

        .able-conversion-wrap {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f2f5;
        }

        html.dark .able-conversion-wrap {
            border-bottom-color: rgba(255, 255, 255, 0.06);
        }

        .able-conversion-metric-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        .able-conversion-val {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #1d2630;
        }

        html.dark .able-conversion-val {
            color: #f8fafc;
        }

        .able-conversion-sub {
            font-size: 12px;
            color: #5b6b79;
            font-weight: 500;
        }

        html.dark .able-conversion-sub {
            color: #94a3b8;
        }

        .able-progress-track {
            width: 100%;
            height: 7px;
            background: #eef2f6;
            border-radius: 9999px;
            overflow: hidden;
        }

        html.dark .able-progress-track {
            background: rgba(255, 255, 255, 0.08);
        }

        .able-progress-fill {
            height: 100%;
            border-radius: 9999px;
            background: #4680ff;
            transition: width 0.4s ease;
        }

        /* Able Pro 4-Stage Micro Cards */
        .able-stage-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .able-stage-box {
            padding: 8px 10px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #e8ebee;
            text-align: center;
        }

        html.dark .able-stage-box {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.06);
        }

        .able-stage-count {
            font-size: 14px;
            font-weight: 700;
            color: #1d2630;
        }

        html.dark .able-stage-count {
            color: #f8fafc;
        }

        .able-stage-lbl {
            font-size: 10px;
            font-weight: 600;
            color: #899bb5;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 2px;
        }

        /* Recent Activity Status List */
        .able-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            font-weight: 700;
            color: #5b6b79;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        html.dark .able-list-header {
            color: #94a3b8;
        }

        .able-list-link {
            font-size: 11.5px;
            font-weight: 600;
            color: #4680ff;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .able-list-link:hover {
            color: #3b6fd8;
            text-decoration: underline;
        }

        .able-quote-list {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .able-quote-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #e8ebee;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        html.dark .able-quote-row {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.06);
        }

        .able-quote-row:hover {
            border-color: #4680ff;
            background: #f8faff;
            transform: translateX(2px);
        }

        html.dark .able-quote-row:hover {
            border-color: #4680ff;
            background: rgba(70, 128, 255, 0.08);
        }

        .able-quote-left {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }

        .able-status-dot {
            width: 8px;
            height: 8px;
            min-width: 8px;
            border-radius: 50%;
        }

        .able-dot-draft { background-color: #899bb5; }
        .able-dot-sent { background-color: #4680ff; }
        .able-dot-accepted { background-color: #2ca87f; }
        .able-dot-invoiced { background-color: #6366f1; }

        .able-quote-customer {
            font-size: 12.5px;
            font-weight: 600;
            color: #1d2630;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 155px;
        }

        html.dark .able-quote-customer {
            color: #f8fafc;
        }

        .able-quote-kw {
            font-size: 11px;
            font-weight: 500;
            color: #5b6b79;
            background: #eef2f6;
            padding: 2px 7px;
            border-radius: 4px;
        }

        html.dark .able-quote-kw {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
        }

        .able-quote-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .able-quote-number {
            font-size: 11.5px;
            color: #899bb5;
            font-family: ui-monospace, monospace;
        }

        .able-quote-amount {
            font-size: 12px;
            font-weight: 700;
            color: #1d2630;
        }

        html.dark .able-quote-amount {
            color: #f8fafc;
        }
    </style>

    <!-- Header & Conversion Progress Bar -->
    <div class="able-conversion-wrap">
        <div class="able-card-header">
            <span class="able-card-title">Quotation Pipeline & Conversion</span>
            <span class="able-conversion-sub">{{ $invoicedCount }} / {{ $totalQuotesCount }} Invoiced</span>
        </div>

        <div class="able-conversion-metric-row">
            <span class="able-conversion-val">{{ $conversionRate }}%</span>
            <span class="able-conversion-sub">Conversion Rate</span>
        </div>

        <div class="able-progress-track">
            <div class="able-progress-fill" style="width: {{ min(100, max(0, $conversionRate)) }}%;"></div>
        </div>
    </div>

    <!-- Pipeline Stages Row -->
    <div class="able-stage-row">
        <div class="able-stage-box">
            <div class="able-stage-count">{{ $pipeline['draft']['count'] }}</div>
            <div class="able-stage-lbl">Draft</div>
        </div>
        <div class="able-stage-box">
            <div class="able-stage-count">{{ $pipeline['sent']['count'] }}</div>
            <div class="able-stage-lbl">Sent</div>
        </div>
        <div class="able-stage-box">
            <div class="able-stage-count">{{ $pipeline['accepted']['count'] }}</div>
            <div class="able-stage-lbl">Accepted</div>
        </div>
        <div class="able-stage-box">
            <div class="able-stage-count">{{ $pipeline['invoiced']['count'] }}</div>
            <div class="able-stage-lbl">Invoiced</div>
        </div>
    </div>

    <!-- Recent Status List -->
    <div>
        <div class="able-list-header" style="margin-bottom: 9px;">
            <span>Recent Pipeline Activity</span>
            <a href="{{ $quotationsUrl }}" class="able-list-link">View All &rarr;</a>
        </div>

        @if($recentQuotations->isNotEmpty())
            <div class="able-quote-list">
                @foreach($recentQuotations as $quote)
                    @php
                        $status = strtolower($quote->status ?? 'draft');
                        $dotClass = match($status) {
                            'sent' => 'able-dot-sent',
                            'accepted' => 'able-dot-accepted',
                            'invoiced' => 'able-dot-invoiced',
                            default => 'able-dot-draft',
                        };
                        $customerName = $quote->contact?->name ?? 'Direct Customer';
                        $kwText = $quote->system_kw ? number_format($quote->system_kw, 1) . ' kWp' : 'PV';
                    @endphp
                    <a href="{{ \App\Filament\App\Resources\Quotations\QuotationResource::getUrl('edit', ['record' => $quote->id]) }}" class="able-quote-row" title="{{ $customerName }} - {{ $quote->quote_number }}">
                        <div class="able-quote-left">
                            <span class="able-status-dot {{ $dotClass }}" title="{{ ucfirst($status) }}"></span>
                            <span class="able-quote-customer">{{ $customerName }}</span>
                            <span class="able-quote-kw">{{ $kwText }}</span>
                        </div>
                        <div class="able-quote-right">
                            <span class="able-quote-number">{{ $quote->quote_number }}</span>
                            <span class="able-quote-amount">Rs. {{ number_format($quote->grand_total, 0) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="font-size: 12px; color: #899bb5; padding: 14px 0; text-align: center;">
                No quotations recorded yet.
            </div>
        @endif
    </div>
</div>
