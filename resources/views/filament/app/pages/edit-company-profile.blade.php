<x-filament-panels::page>
    @php
        $tenant = $this->tenant ?? \Filament\Facades\Filament::getTenant();
        $settings = $tenant->settings ?? [];
        if (! is_array($settings)) {
            $settings = [];
        }

        $primaryColor = $settings['primary_color'] ?? '#047857';
        $tableHeaderColor = $settings['table_header_color'] ?? '#047857';
        $textColor = $settings['text_color'] ?? '#ffffff';
        $bankDetails = $settings['bank_details'] ?? '';
        $defaultInvoiceTerms = $settings['default_invoice_terms'] ?? ($settings['default_quote_terms'] ?? '');
        $quotePrefix = $settings['quote_prefix'] ?? 'QUO-2026-';
        $invoicePrefix = $settings['invoice_prefix'] ?? 'INV-2026-';

        $logoPath = $tenant->logo_path ?? ($settings['logo_path'] ?? null);
        $logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;

        $signaturePath = $settings['signature_path'] ?? null;
        $signatureUrl = $signaturePath ? asset('storage/' . $signaturePath) : null;
    @endphp

    <div
        x-data="invoiceBrandingLivePreview({
            primaryColor: @js($primaryColor),
            tableHeaderColor: @js($tableHeaderColor),
            textColor: @js($textColor),
            companyName: @js($tenant->name ?? 'Vertex Solar Technologies (Pvt) Ltd'),
            phone: @js($tenant->phone ?? '+94 11 234 5678'),
            email: @js($tenant->email ?? 'billing@vertexsolar.lk'),
            address: @js($tenant->address ?? 'No. 142/A, High Level Road, Colombo 05, Sri Lanka'),
            taxNumber: @js($tenant->tax_number ?? 'VAT-100293847'),
            bankDetails: @js($bankDetails),
            defaultInvoiceTerms: @js($defaultInvoiceTerms),
            quotePrefix: @js($quotePrefix),
            invoicePrefix: @js($invoicePrefix),
            logoUrl: @js($logoUrl),
            signatureUrl: @js($signatureUrl),
        })"
        x-init="init()"
        @input="onFormInput($event)"
        @change="onFormChange($event)"
        class="branding-split-container"
    >
        <style>
            .branding-split-container {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                width: 100%;
            }

            @media (min-width: 1024px) {
                .branding-split-container {
                    flex-direction: row;
                    align-items: flex-start;
                    gap: 1.75rem;
                }
                .branding-form-col {
                    flex: 1 1 50%;
                    min-width: 0;
                    max-width: 50%;
                }
                .branding-preview-col {
                    flex: 1 1 50%;
                    min-width: 0;
                    max-width: 50%;
                    position: sticky;
                    top: 4.5rem;
                    max-height: calc(100vh - 5.5rem);
                    overflow-y: auto;
                    padding-right: 4px;
                }
            }

            /* Custom sleek scrollbar for preview */
            .branding-preview-col::-webkit-scrollbar {
                width: 6px;
            }
            .branding-preview-col::-webkit-scrollbar-track {
                background: transparent;
            }
            .branding-preview-col::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }
            .dark .branding-preview-col::-webkit-scrollbar-thumb {
                background: #334155;
            }

            /* Studio Workspace Canvas */
            .preview-canvas {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 16px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }
            .dark .preview-canvas {
                background: #0f172a;
                border-color: #1e293b;
            }

            /* Realistic A4 Paper sheet */
            .a4-mockup-paper {
                background: #ffffff;
                color: #1e293b;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.1), 0 4px 10px -2px rgba(0, 0, 0, 0.05);
                position: relative;
                overflow: hidden;
                padding: 28px 30px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                font-size: 11px;
                line-height: 1.45;
                transition: transform 0.2s ease;
                transform-origin: top center;
            }

            /* Angle Corner Ribbon */
            .mockup-corner-ribbon {
                position: absolute;
                top: 18px;
                right: -38px;
                width: 140px;
                text-align: center;
                transform: rotate(45deg);
                font-size: 9px;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                padding: 4px 0;
                color: #ffffff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                pointer-events: none;
                z-index: 10;
            }

            /* Custom table borders */
            .mockup-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 16px;
            }
            .mockup-table th {
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                padding: 7px 9px;
            }
            .mockup-table td {
                padding: 7px 9px;
                font-size: 10.5px;
                border-bottom: 1px solid #f1f5f9;
            }

            .info-card-box {
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                padding: 10px 12px;
            }

            .pulse-dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #10b981;
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                animation: pulseAnimation 2s infinite;
            }
            @keyframes pulseAnimation {
                0% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
                }
                70% {
                    transform: scale(1);
                    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
                }
                100% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
                }
            }
        </style>

        {{-- LEFT COLUMN: 50% Settings Form --}}
        <div class="branding-form-col">
            {{ $this->content }}
        </div>

        {{-- RIGHT COLUMN: 50% Sticky Live Document Preview --}}
        <div class="branding-preview-col">
            <div class="preview-canvas">
                {{-- Preview Controls Toolbar --}}
                <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="pulse-dot"></span>
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wide">
                            Live Interactive Document Preview
                        </span>
                        <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 font-semibold px-2 py-0.5 rounded-full border border-emerald-300 dark:border-emerald-800">
                            Instant Client-Side Sync
                        </span>
                    </div>

                    {{-- Document Type Selector --}}
                    <div class="flex items-center bg-gray-200 dark:bg-gray-800 rounded-lg p-0.5 text-xs font-semibold">
                        <button
                            type="button"
                            @click="docType = 'invoice'"
                            :class="docType === 'invoice' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900'"
                            class="px-2.5 py-1 rounded-md transition"
                        >
                            Invoice
                        </button>
                        <button
                            type="button"
                            @click="docType = 'quotation'"
                            :class="docType === 'quotation' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900'"
                            class="px-2.5 py-1 rounded-md transition"
                        >
                            Quotation
                        </button>
                    </div>
                </div>

                {{-- Scale toolbar --}}
                <div class="flex items-center justify-between text-[11px] text-gray-500 mb-3 px-1">
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-400">Preview Scale:</span>
                        <button
                            type="button"
                            @click="zoom = 0.85"
                            :class="zoom === 0.85 ? 'text-primary-600 font-bold bg-white dark:bg-gray-800 border-gray-300 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                            class="px-2 py-0.5 rounded border border-transparent transition"
                        >
                            85%
                        </button>
                        <button
                            type="button"
                            @click="zoom = 0.95"
                            :class="zoom === 0.95 ? 'text-primary-600 font-bold bg-white dark:bg-gray-800 border-gray-300 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                            class="px-2 py-0.5 rounded border border-transparent transition"
                        >
                            95%
                        </button>
                        <button
                            type="button"
                            @click="zoom = 1.0"
                            :class="zoom === 1.0 ? 'text-primary-600 font-bold bg-white dark:bg-gray-800 border-gray-300 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                            class="px-2 py-0.5 rounded border border-transparent transition"
                        >
                            100%
                        </button>
                    </div>

                    <div class="text-[10px] text-gray-400 italic">
                        Updates automatically as you type or pick colors
                    </div>
                </div>

                {{-- REALISTIC A4 INVOICE SHEET --}}
                <div
                    class="a4-mockup-paper"
                    :style="'transform: scale(' + zoom + '); --brand-primary: ' + primaryColor + '; --table-header: ' + tableHeaderColor + '; --header-text: ' + textColor + ';'"
                >
                    {{-- Angle Corner Ribbon --}}
                    <div
                        class="mockup-corner-ribbon"
                        :style="'background-color: ' + primaryColor"
                        x-text="docType === 'quotation' ? 'QUOTE' : 'SAMPLE'"
                    >
                        SAMPLE
                    </div>

                    {{-- Top Brand Accent Bar --}}
                    <div
                        style="height: 4px; border-radius: 2px; margin-bottom: 20px;"
                        :style="'background: linear-gradient(90deg, ' + primaryColor + ' 0%, ' + tableHeaderColor + ' 100%);'"
                    ></div>

                    {{-- DOCUMENT HEADER --}}
                    <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 20px; margin-bottom: 20px; align-items: start;">
                        <div>
                            {{-- Dynamic Logo with Text Fallback --}}
                            <template x-if="logoUrl">
                                <img
                                    :src="logoUrl"
                                    alt="Company Official Logo"
                                    style="max-height: 46px; max-width: 180px; object-fit: contain; margin-bottom: 6px; display: block;"
                                />
                            </template>

                            <h2
                                class="font-extrabold tracking-tight"
                                style="font-size: 18px; line-height: 1.2; margin: 0 0 4px 0;"
                                :style="'color: ' + primaryColor"
                                x-text="companyName || 'Your Company Name'"
                            >
                                Your Company Name
                            </h2>

                            <div style="font-size: 10.5px; color: #475569; line-height: 1.45;">
                                <div x-text="address || 'No. 142/A, High Level Road, Colombo 05, Sri Lanka'"></div>
                                <div>
                                    <span x-text="'Tel: ' + (phone || '+94 11 234 5678')"></span>
                                    <span> • </span>
                                    <span x-text="'Email: ' + (email || 'billing@company.lk')"></span>
                                </div>
                                <div x-show="taxNumber || true" style="margin-top: 2px;">
                                    <span style="font-weight: 600; color: #334155;">Tax / VAT / TIN: </span>
                                    <strong x-text="taxNumber || 'VAT-100293847'" style="color: #0f172a;"></strong>
                                </div>
                            </div>
                        </div>

                        {{-- Right Title & Document Metadata --}}
                        <div style="text-align: right;">
                            <div
                                style="display: inline-block; font-size: 12px; font-weight: 800; padding: 5px 12px; border-radius: 4px; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 8px;"
                                :style="'background-color: ' + primaryColor + '; color: #ffffff;'"
                                x-text="docType === 'quotation' ? 'SOLAR PV QUOTATION' : 'TAX INVOICE'"
                            >
                                TAX INVOICE
                            </div>

                            <div style="font-size: 11px; color: #334155; line-height: 1.55;">
                                <div>
                                    <span style="font-weight: 700;">Doc No: </span>
                                    <span x-text="docType === 'quotation' ? (quotePrefix + '001') : (invoicePrefix + '001')">#INV-2026-001</span>
                                </div>
                                <div>
                                    <span style="font-weight: 600;">Issue Date: </span>
                                    <span>{{ now()->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <span style="font-weight: 600;" x-text="docType === 'quotation' ? 'Valid Until: ' : 'Due Date: '">Due Date: </span>
                                    <span>{{ now()->addDays(14)->format('d M Y') }}</span>
                                </div>
                                <div style="margin-top: 4px;">
                                    <span
                                        style="font-size: 9.5px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; border: 1px solid;"
                                        :style="'color: ' + primaryColor + '; border-color: ' + primaryColor + '; background-color: ' + primaryColor + '15;'"
                                        x-text="docType === 'quotation' ? 'VALID QUOTE' : 'ISSUED / UNPAID'"
                                    >
                                        ISSUED / UNPAID
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CUSTOMER & GRID CARDS --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                        {{-- Bill To Card --}}
                        <div class="info-card-box">
                            <div
                                style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;"
                                :style="'color: ' + primaryColor"
                            >
                                Bill To / Customer Information
                            </div>
                            <div style="font-weight: 700; font-size: 11.5px; color: #0f172a;">Mr. Samantha Perera</div>
                            <div style="font-size: 10.5px; color: #475569;">No. 78, Temple Road, Mount Lavinia</div>
                            <div style="font-size: 10.5px; color: #475569;">Phone: +94 77 789 1234</div>
                        </div>

                        {{-- Technical / CEB Utility Card --}}
                        <div class="info-card-box">
                            <div
                                style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;"
                                :style="'color: ' + primaryColor"
                            >
                                Grid Connection & Engineering
                            </div>
                            <div style="font-size: 10.5px; color: #334155;">Utility: Ceylon Electricity Board (CEB)</div>
                            <div style="margin-top: 3px;">
                                <span style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 700; font-size: 10px; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                                    CEB Acc: 082-192-4820
                                </span>
                            </div>
                            <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                                System: <strong>10.0 kWp Three-Phase</strong> (Net Plus)
                            </div>
                        </div>
                    </div>

                    {{-- DUMMY SOLAR PV ITEM TABLE --}}
                    <table class="mockup-table">
                        <thead>
                            <tr :style="'background-color: ' + tableHeaderColor + '; color: ' + textColor + ';'">
                                <th style="width: 5%; text-align: left; border-radius: 4px 0 0 4px;">#</th>
                                <th style="width: 48%; text-align: left;">Item & Technical Specifications</th>
                                <th style="width: 12%; text-align: right;">Qty</th>
                                <th style="width: 17%; text-align: right;">Rate (LKR)</th>
                                <th style="width: 18%; text-align: right; border-radius: 0 4px 4px 0;">Amount (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background-color: transparent;">
                                <td style="text-align: left; color: #64748b;">1</td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;">Tier 1 550W Mono PERC Half-Cell Solar Panels</div>
                                    <div style="font-size: 9.5px; color: #64748b;">18x High efficiency photovoltaic modules, 25-year linear performance warranty</div>
                                </td>
                                <td style="text-align: right; color: #334155;">18.00</td>
                                <td style="text-align: right; color: #334155;">34,500.00</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">621,000.00</td>
                            </tr>
                            <tr style="background-color: #f8fafc;">
                                <td style="text-align: left; color: #64748b;">2</td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;">Huawei SUN2000-10KTL-M1 10kW On-Grid Inverter</div>
                                    <div style="font-size: 9.5px; color: #64748b;">Three-phase dual MPPT, AI-powered AFCI arc fault protection, WLAN smart dongle</div>
                                </td>
                                <td style="text-align: right; color: #334155;">1.00</td>
                                <td style="text-align: right; color: #334155;">485,000.00</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">485,000.00</td>
                            </tr>
                            <tr style="background-color: transparent;">
                                <td style="text-align: left; color: #64748b;">3</td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;">Aluminum Rail Mounting Structure & DC/AC Protection</div>
                                    <div style="font-size: 9.5px; color: #64748b;">Anodized aluminum roof rails, stainless fasteners, Type II surge arresters & DC isolators</div>
                                </td>
                                <td style="text-align: right; color: #334155;">1.00</td>
                                <td style="text-align: right; color: #334155;">165,000.00</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">165,000.00</td>
                            </tr>
                            <tr style="background-color: #f8fafc;">
                                <td style="text-align: left; color: #64748b;">4</td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;">CEB Net Metering Consultation & Commissioning</div>
                                    <div style="font-size: 9.5px; color: #64748b;">Utility inspection facilitation, compliance certification, and grid testing report</div>
                                </td>
                                <td style="text-align: right; color: #334155;">1.00</td>
                                <td style="text-align: right; color: #334155;">45,000.00</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">45,000.00</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- SUMMARY BOX & REMITTANCE FOOTER --}}
                    <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 18px; align-items: start;">
                        {{-- Left: Milestones, Bank & Terms --}}
                        <div>
                            {{-- Milestone Payment Breakdown --}}
                            <div class="info-card-box" style="margin-bottom: 10px; padding: 8px 10px;">
                                <div style="font-weight: 700; font-size: 10px; color: #0f172a; margin-bottom: 4px; display: flex; justify-content: space-between;">
                                    <span>Milestone Payment Schedule:</span>
                                    <span :style="'color: ' + primaryColor" style="font-weight: 800;">60% - 20% - 20%</span>
                                </div>
                                <div style="font-size: 9.5px; color: #475569; display: grid; grid-template-columns: 1fr 1fr; gap: 4px;">
                                    <div>• 60% Advance Confirmation:</div>
                                    <div style="text-align: right; font-weight: 600;">Rs. 789,600.00</div>
                                    <div>• 20% Material Delivery:</div>
                                    <div style="text-align: right; font-weight: 600;">Rs. 263,200.00</div>
                                    <div>• 20% Grid Commissioning:</div>
                                    <div style="text-align: right; font-weight: 600;">Rs. 263,200.00</div>
                                </div>
                            </div>

                            {{-- Bank Wire Remittance --}}
                            <div class="info-card-box" style="margin-bottom: 10px; padding: 8px 10px;">
                                <div style="font-weight: 700; font-size: 10px; color: #0f172a; margin-bottom: 2px;">
                                    🏦 Electronic Bank Wire Remittance:
                                </div>
                                <div
                                    style="font-size: 9.5px; color: #475569; line-height: 1.45; white-space: pre-line;"
                                    x-text="bankDetails || '• Bank of Ceylon (BOC) | Acc: 97194769 (Branch: Colombo Fort)\n• DFCC Bank | Acc: 101001490231 (Branch: Corporate Head Office)'"
                                >
                                </div>
                            </div>

                            {{-- Terms & Conditions --}}
                            <div style="font-size: 9.5px; color: #64748b; line-height: 1.4;">
                                <div style="font-weight: 700; color: #334155; margin-bottom: 2px;">Terms & Conditions:</div>
                                <div
                                    style="white-space: pre-line;"
                                    x-text="defaultInvoiceTerms || '1. Valid for 14 calendar days from date of issue.\n2. Inverter warranty 10 years, Solar panels 25 years performance warranty.'"
                                >
                                </div>
                            </div>
                        </div>

                        {{-- Right: Totals & Digital Stamp --}}
                        <div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="padding: 4px 6px; color: #475569;">Subtotal:</td>
                                    <td style="padding: 4px 6px; text-align: right; font-weight: 600; color: #0f172a;">Rs. 1,316,000.00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 6px; color: #475569;">SSCL / Tax (2.5%):</td>
                                    <td style="padding: 4px 6px; text-align: right; color: #475569;">Rs. 32,900.00</td>
                                </tr>
                                <tr
                                    style="font-weight: 800; font-size: 12px;"
                                    :style="'background-color: ' + primaryColor + '; color: #ffffff;'"
                                >
                                    <td style="padding: 7px 9px; border-radius: 4px 0 0 4px;">Grand Total:</td>
                                    <td style="padding: 7px 9px; text-align: right; border-radius: 0 4px 4px 0;">Rs. 1,348,900.00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 6px 2px 6px; font-weight: 700; color: #334155;">Balance Due:</td>
                                    <td
                                        style="padding: 6px 6px 2px 6px; text-align: right; font-weight: 800; font-size: 12px;"
                                        :style="'color: ' + primaryColor"
                                    >
                                        Rs. 1,348,900.00
                                    </td>
                                </tr>
                            </table>

                            {{-- Authorized Signature & Stamp Preview --}}
                            <div style="margin-top: 18px; text-align: right;">
                                <template x-if="signatureUrl">
                                    <div style="display: inline-block; text-align: center;">
                                        <img
                                            :src="signatureUrl"
                                            alt="Authorized Signature & Stamp"
                                            style="max-height: 48px; max-width: 140px; display: inline-block; object-fit: contain;"
                                        />
                                        <div style="font-size: 9.5px; font-weight: 700; color: #475569; border-top: 1px solid #cbd5e1; padding-top: 3px; min-width: 130px; margin-top: 4px;">
                                            Authorized Signatory
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!signatureUrl">
                                    <div
                                        style="display: inline-block; padding: 8px 12px; border: 1.5px dashed; border-radius: 6px; text-align: center; min-width: 140px;"
                                        :style="'border-color: ' + primaryColor + '60; background-color: ' + primaryColor + '08;'"
                                    >
                                        <div
                                            style="font-weight: 800; font-size: 9.5px; letter-spacing: 0.05em; text-transform: uppercase;"
                                            :style="'color: ' + primaryColor"
                                        >
                                            AUTHORIZED STAMP
                                        </div>
                                        <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">
                                            Digital Verification Stamp
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            Alpine.data('invoiceBrandingLivePreview', (initialData) => ({
                primaryColor: initialData.primaryColor || '#047857',
                tableHeaderColor: initialData.tableHeaderColor || '#047857',
                textColor: initialData.textColor || '#ffffff',
                companyName: initialData.companyName || '',
                phone: initialData.phone || '',
                email: initialData.email || '',
                address: initialData.address || '',
                taxNumber: initialData.taxNumber || '',
                bankDetails: initialData.bankDetails || '',
                defaultInvoiceTerms: initialData.defaultInvoiceTerms || '',
                quotePrefix: initialData.quotePrefix || 'QUO-2026-',
                invoicePrefix: initialData.invoicePrefix || 'INV-2026-',
                logoUrl: initialData.logoUrl || null,
                signatureUrl: initialData.signatureUrl || null,
                docType: 'invoice',
                zoom: 1.0,

                init() {
                    // Watch Livewire properties if available
                    if (this.$wire) {
                        this.$watch('$wire.data.settings.primary_color', (val) => {
                            if (val) this.primaryColor = val;
                        });
                        this.$watch('$wire.data.settings.table_header_color', (val) => {
                            if (val) this.tableHeaderColor = val;
                        });
                        this.$watch('$wire.data.settings.text_color', (val) => {
                            if (val) this.textColor = val;
                        });
                        this.$watch('$wire.data.name', (val) => {
                            if (val) this.companyName = val;
                        });
                        this.$watch('$wire.data.phone', (val) => {
                            if (val) this.phone = val;
                        });
                        this.$watch('$wire.data.email', (val) => {
                            if (val) this.email = val;
                        });
                        this.$watch('$wire.data.address', (val) => {
                            if (val) this.address = val;
                        });
                        this.$watch('$wire.data.tax_number', (val) => {
                            if (val) this.taxNumber = val;
                        });
                        this.$watch('$wire.data.settings.bank_details', (val) => {
                            if (val) this.bankDetails = val;
                        });
                        this.$watch('$wire.data.settings.default_invoice_terms', (val) => {
                            if (val) this.defaultInvoiceTerms = val;
                        });
                        this.$watch('$wire.data.settings.quote_prefix', (val) => {
                            if (val) this.quotePrefix = val;
                        });
                        this.$watch('$wire.data.settings.invoice_prefix', (val) => {
                            if (val) this.invoicePrefix = val;
                        });
                    }
                },

                onFormInput(e) {
                    const el = e.target;
                    if (!el) return;
                    const name = (el.getAttribute('name') || el.getAttribute('id') || el.getAttribute('wire:model') || '').toLowerCase();
                    const val = el.value;

                    if (name.includes('primary_color')) {
                        this.primaryColor = val;
                    } else if (name.includes('table_header_color')) {
                        this.tableHeaderColor = val;
                    } else if (name.includes('text_color')) {
                        this.textColor = val;
                    } else if (name.includes('quote_prefix')) {
                        this.quotePrefix = val;
                    } else if (name.includes('invoice_prefix')) {
                        this.invoicePrefix = val;
                    } else if (name.endsWith('.name') || name === 'name' || name.includes('data.name')) {
                        this.companyName = val;
                    } else if (name.includes('phone')) {
                        this.phone = val;
                    } else if (name.includes('email')) {
                        this.email = val;
                    } else if (name.includes('tax_number')) {
                        this.taxNumber = val;
                    } else if (name.includes('address')) {
                        this.address = val;
                    } else if (name.includes('bank_details')) {
                        this.bankDetails = val;
                    } else if (name.includes('default_invoice_terms') || name.includes('default_quote_terms')) {
                        this.defaultInvoiceTerms = val;
                    }
                },

                onFormChange(e) {
                    this.onFormInput(e);

                    const el = e.target;
                    if (el && el.type === 'file' && el.files && el.files[0]) {
                        const file = el.files[0];
                        const reader = new FileReader();
                        const idOrName = (el.getAttribute('name') || el.getAttribute('id') || '').toLowerCase();
                        const isSignature = idOrName.includes('signature') ||
                            Boolean(el.closest('[wire\\:id]')?.innerText?.toLowerCase().includes('signature'));

                        reader.onload = (event) => {
                            if (isSignature) {
                                this.signatureUrl = event.target.result;
                            } else {
                                this.logoUrl = event.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                },

                setPrimaryColor(hex) {
                    this.primaryColor = hex;
                    this.updateField('settings.primary_color', hex);
                },

                setTableHeaderColor(hex) {
                    this.tableHeaderColor = hex;
                    this.updateField('settings.table_header_color', hex);
                },

                setTextColor(hex) {
                    this.textColor = hex;
                    this.updateField('settings.text_color', hex);
                },

                applyPalette(primary, header, text) {
                    this.setPrimaryColor(primary);
                    this.setTableHeaderColor(header);
                    this.setTextColor(text);
                },

                updateField(path, val) {
                    // Update DOM input directly
                    const inputs = document.querySelectorAll(`input[id*="${path}"], input[name*="${path}"]`);
                    inputs.forEach(input => {
                        input.value = val;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    // Sync Livewire state directly
                    if (this.$wire) {
                        try {
                            this.$wire.set('data.' + path, val);
                        } catch (err) {
                            console.debug('Livewire set', err);
                        }
                    }
                }
            }));
        </script>
    @endscript
</x-filament-panels::page>
