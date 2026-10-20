<div class="linear-action-bar-wrap">
    <style>
        .linear-action-bar-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 10px;
            box-shadow: none;
        }

        html.dark .linear-action-bar-wrap {
            background: #121316;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: none;
        }

        .linear-bar-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 500;
            color: #a1a1aa;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .linear-bar-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 6px rgba(16, 185, 129, 0.4);
        }

        .linear-action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .linear-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            color: #18181b;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            line-height: 1.4;
        }

        .linear-btn-pill:hover {
            background: #f4f4f5;
            border-color: #d4d4d8;
            color: #09090b;
        }

        html.dark .linear-btn-pill {
            background: #18181b;
            border: 1px solid #27272a;
            color: #f4f4f5;
        }

        html.dark .linear-btn-pill:hover {
            background: #27272a;
            border-color: #3f3f46;
            color: #ffffff;
        }
    </style>

    <div class="linear-bar-label">
        <span class="linear-bar-dot"></span>
        <span>Quick Actions</span>
    </div>

    <div class="linear-action-buttons">
        <a href="{{ $newQuoteUrl }}" class="linear-btn-pill" id="action-btn-new-quote">
            <span style="color: #10b981; font-weight: 600;">+</span> New Quote
        </a>

        <a href="{{ $newInvoiceUrl }}" class="linear-btn-pill" id="action-btn-new-invoice">
            <span style="color: #3b82f6; font-weight: 600;">+</span> New Invoice
        </a>

        <a href="{{ $posTerminalUrl }}" class="linear-btn-pill" id="action-btn-pos">
            <span style="color: #8b5cf6; font-weight: 600;">+</span> POS
        </a>

        <a href="{{ $issueCertificateUrl }}" class="linear-btn-pill" id="action-btn-certificate">
            <span style="color: #f59e0b; font-weight: 600;">+</span> Issue Certificate
        </a>
    </div>
</div>
