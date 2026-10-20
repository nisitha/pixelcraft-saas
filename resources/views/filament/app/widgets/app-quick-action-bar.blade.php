<div class="able-action-bar-wrap">
    <style>
        .able-action-bar-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 12px 18px;
            background: #ffffff;
            border: 1px solid #e8ebee;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(33, 40, 50, 0.05);
        }

        html.dark .able-action-bar-wrap {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }

        .able-bar-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11.5px;
            font-weight: 700;
            color: #5b6b79;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        html.dark .able-bar-label {
            color: #94a3b8;
        }

        .able-bar-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #4680ff;
            box-shadow: 0 0 8px rgba(70, 128, 255, 0.5);
        }

        .able-action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Able Pro Primary Brand Button */
        .able-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #4680ff;
            border: 1px solid #4680ff;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(70, 128, 255, 0.28);
        }

        .able-btn-primary:hover {
            background: #3b6fd8;
            border-color: #3b6fd8;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(70, 128, 255, 0.35);
        }

        /* Able Pro Secondary Outline Pill Buttons */
        .able-btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8f9fa;
            border: 1px solid #e8ebee;
            color: #1d2630;
            font-size: 12.5px;
            font-weight: 500;
            padding: 6px 13px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }

        .able-btn-pill:hover {
            background: #eef4fe;
            border-color: #4680ff;
            color: #4680ff;
            transform: translateY(-1px);
        }

        html.dark .able-btn-pill {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }

        html.dark .able-btn-pill:hover {
            background: rgba(70, 128, 255, 0.15);
            border-color: #4680ff;
            color: #4680ff;
        }
    </style>

    <div class="able-bar-label">
        <span class="able-bar-dot"></span>
        <span>Quick Actions</span>
    </div>

    <div class="able-action-buttons">
        <a href="{{ $newQuoteUrl }}" class="able-btn-primary" id="action-btn-new-quote">
            <span style="font-weight: 700;">+</span> New Quote
        </a>

        <a href="{{ $newInvoiceUrl }}" class="able-btn-pill" id="action-btn-new-invoice">
            <span style="color: #4680ff; font-weight: 700;">+</span> New Invoice
        </a>

        <a href="{{ $posTerminalUrl }}" class="able-btn-pill" id="action-btn-pos">
            <span style="color: #8b5cf6; font-weight: 700;">+</span> POS
        </a>

        <a href="{{ $issueCertificateUrl }}" class="able-btn-pill" id="action-btn-certificate">
            <span style="color: #2ca87f; font-weight: 700;">+</span> Issue Certificate
        </a>
    </div>
</div>
