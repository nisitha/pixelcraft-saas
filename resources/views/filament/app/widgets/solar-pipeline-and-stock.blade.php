<div class="zoho-pipeline-stock-card">
    <style>
        .zoho-pipeline-stock-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: 100%;
        }

        html.dark .zoho-pipeline-stock-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .zoho-sec-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        html.dark .zoho-sec-title {
            color: #94a3b8;
        }

        /* 4-Column Pipeline Grid */
        .zoho-pipe-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        @media (max-width: 640px) {
            .zoho-pipe-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .zoho-pipe-box {
            padding: 10px 8px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: all 0.15s ease;
        }

        html.dark .zoho-pipe-box {
            background: #1f2937;
            border-color: #374151;
        }

        .zoho-pipe-box:hover {
            transform: translateY(-1px);
        }

        .zoho-pipe-count {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }

        .zoho-pipe-lbl {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
        }

        html.dark .zoho-pipe-lbl {
            color: #94a3b8;
        }

        .zoho-pipe-val {
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            margin-top: 4px;
        }

        html.dark .zoho-pipe-val {
            color: #cbd5e1;
        }

        /* Stock Alert List */
        .zoho-stock-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .zoho-stock-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            font-size: 12px;
        }

        html.dark .zoho-stock-row {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .zoho-stock-name {
            font-weight: 600;
            color: #991b1b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        html.dark .zoho-stock-name {
            color: #fca5a5;
        }

        .zoho-stock-badge {
            font-size: 10.5px;
            font-weight: 700;
            background: #ef4444;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 9999px;
            white-space: nowrap;
        }

        /* Commission Banner */
        .zoho-comm-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 8px;
            background: #fffbeb;
            border: 1px solid #fef3c7;
        }

        html.dark .zoho-comm-banner {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.2);
        }
    </style>

    <!-- Quotation Pipeline Section -->
    <div>
        <div class="zoho-sec-title" style="margin-bottom: 10px;">
            <span>☀️ Solar Quotation Pipeline</span>
            <a href="{{ $quotationsUrl }}" style="font-size: 11px; text-decoration: none; color: #3b82f6; font-weight: 600;">View All &rarr;</a>
        </div>

        <div class="zoho-pipe-grid">
            <div class="zoho-pipe-box" style="border-top: 3px solid #94a3b8;">
                <div class="zoho-pipe-count" style="color: #64748b;">{{ $pipeline['draft']['count'] }}</div>
                <div class="zoho-pipe-lbl">Draft</div>
                <div class="zoho-pipe-val">Rs. {{ number_format($pipeline['draft']['total'], 0) }}</div>
            </div>

            <div class="zoho-pipe-box" style="border-top: 3px solid #3b82f6;">
                <div class="zoho-pipe-count" style="color: #2563eb;">{{ $pipeline['sent']['count'] }}</div>
                <div class="zoho-pipe-lbl">Sent</div>
                <div class="zoho-pipe-val">Rs. {{ number_format($pipeline['sent']['total'], 0) }}</div>
            </div>

            <div class="zoho-pipe-box" style="border-top: 3px solid #10b981;">
                <div class="zoho-pipe-count" style="color: #059669;">{{ $pipeline['accepted']['count'] }}</div>
                <div class="zoho-pipe-lbl">Accepted</div>
                <div class="zoho-pipe-val">Rs. {{ number_format($pipeline['accepted']['total'], 0) }}</div>
            </div>

            <div class="zoho-pipe-box" style="border-top: 3px solid #6366f1;">
                <div class="zoho-pipe-count" style="color: #4f46e5;">{{ $pipeline['invoiced']['count'] }}</div>
                <div class="zoho-pipe-lbl">Invoiced</div>
                <div class="zoho-pipe-val">Rs. {{ number_format($pipeline['invoiced']['total'], 0) }}</div>
            </div>
        </div>
    </div>

    <!-- Low Stock Inventory Alerts -->
    <div>
        <div class="zoho-sec-title" style="margin-bottom: 8px;">
            <span>⚠️ Low Stock Alerts</span>
            <span style="font-size: 11px; font-weight: 700; color: {{ $totalLowStockCount > 0 ? '#dc2626' : '#059669' }};">
                {{ $totalLowStockCount }} {{ Str::plural('Item', $totalLowStockCount) }} Below Threshold
            </span>
        </div>

        @if($lowStockProducts->isNotEmpty())
            <div class="zoho-stock-list">
                @foreach($lowStockProducts as $prod)
                    <div class="zoho-stock-row">
                        <span class="zoho-stock-name" title="{{ $prod->name }}">{{ $prod->name }}</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 11px; color: #64748b;">Alert: {{ (int) $prod->alert_qty }}</span>
                            <span class="zoho-stock-badge">{{ (int) $prod->stock_qty }} Left</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="padding: 10px 12px; border-radius: 8px; background: #ecfdf5; border: 1px solid #d1fae5; font-size: 11.5px; color: #065f46; display: flex; align-items: center; gap: 6px;">
                <span>✅</span> All inventory items are adequately stocked above alert thresholds.
            </div>
        @endif
    </div>

    <!-- Pending Agent Commissions -->
    <div class="zoho-comm-banner">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em;">
                💼 Pending Sales Commissions
            </div>
            <div style="font-size: 15px; font-weight: 800; color: #b45309; margin-top: 2px;">
                Rs. {{ number_format($pendingCommissionsAmount, 2) }}
            </div>
            <div style="font-size: 10.5px; color: #78350f;">
                {{ $pendingCommissionsCount }} {{ Str::plural('invoice commission', $pendingCommissionsCount) }} awaiting payout
            </div>
        </div>
        <a href="{{ $commissionsUrl }}" style="font-size: 11px; font-weight: 700; color: #b45309; background: #ffffff; border: 1px solid #fde68a; padding: 6px 12px; border-radius: 6px; text-decoration: none;">
            Manage &rarr;
        </a>
    </div>
</div>
