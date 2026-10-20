<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Solar System Installation Agreement - {{ $agreementNumber }}</title>
    @php
        $primaryColor = $company->settings['primary_color'] ?? '#047857';
        $contact = $contact ?? ($record->contact ?? null);
        $grandTotal = (float) ($record->grand_total ?? 1250000.00);
        $milestone1 = round($grandTotal * 0.50, 2);
        $milestone2 = round($grandTotal - $milestone1, 2);

        $resolveImagePath = function (?string $path): ?string {
            if (empty($path)) {
                return null;
            }

            $resolved = null;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                $resolved = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
            } elseif (file_exists(public_path('storage/' . $path))) {
                $resolved = public_path('storage/' . $path);
            } elseif (file_exists(storage_path('app/public/' . $path))) {
                $resolved = storage_path('app/public/' . $path);
            } elseif (file_exists($path)) {
                $resolved = realpath($path) ?: $path;
            }

            if (! $resolved || ! file_exists($resolved)) {
                return null;
            }

            $extension = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
            if ($extension === 'svg') {
                return null;
            }

            return $resolved;
        };

        $logoPath = $resolveImagePath($company->logo_path ?? ($company->settings['logo_path'] ?? null));
        $signaturePath = $resolveImagePath($company->settings['signature_path'] ?? null);
    @endphp
    <style>
        @page {
            size: a4 portrait;
            margin: 14mm 16mm 14mm 16mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-before: always;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid {{ $primaryColor }};
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 16px;
            font-weight: 800;
            color: {{ $primaryColor }};
            letter-spacing: -0.2px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .company-details {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.35;
        }
        .agreement-badge {
            background-color: {{ $primaryColor }};
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .ref-box {
            text-align: right;
            font-size: 9px;
            color: #334155;
            line-height: 1.4;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: 800;
            color: {{ $primaryColor }};
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        .parties-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .parties-table td {
            width: 50%;
            vertical-align: top;
            padding: 6px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .spec-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9px;
        }
        .spec-table th {
            background-color: {{ $primaryColor }};
            color: #ffffff;
            padding: 4px 8px;
            font-size: 8.5px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
        }
        .spec-table td {
            padding: 4.5px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .spec-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9.5px;
        }
        .payment-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
        }
        .bank-box {
            background: #f1f5f9;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 8.5px;
            color: #334155;
            line-height: 1.4;
            margin-top: 6px;
        }
        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 8px;
            color: #64748b;
            text-align: center;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        .signature-table td {
            width: 33.33%;
            vertical-align: top;
            padding: 8px;
            border: 1px solid #e2e8f0;
            background: #fafafa;
        }
        .signature-line {
            border-bottom: 1px dashed #64748b;
            height: 42px;
            margin-bottom: 6px;
        }
        .badge-pill {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- ==================== PAGE 1 ==================== --}}
    <div>
        {{-- Header --}}
        <table class="header-table">
            <tr>
                <td style="width: 58%;">
                    @if ($logoPath)
                        <img src="{{ $logoPath }}" style="max-height: 44px; max-width: 180px; margin-bottom: 4px;" alt="Logo"><br>
                    @endif
                    <div class="company-name">{{ $company->name }}</div>
                    <div class="company-details">
                        {{ $company->address ?? 'No. 33/A, Hapugahagedara, Loluwagoda, Mirigama, Sri Lanka' }}<br>
                        Tel: {{ $company->phone ?? '+94 70 2 271 271' }} | Email: {{ $company->email ?? 'operations@vertexhomeslk.com' }}<br>
                        Business Reg: PV 00291032 | Tax TIN: {{ $company->tax_number ?? 'TIN-10293847-001' }}
                    </div>
                </td>
                <td style="width: 42%; text-align: right;">
                    <div class="agreement-badge">Installation Agreement</div>
                    <div class="ref-box">
                        <strong>Agreement No:</strong> {{ $agreementNumber }}<br>
                        <strong>Date of Issue:</strong> {{ $agreementDate }}<br>
                        <strong>Quotation Ref:</strong> {{ $quotationNumber ?? $agreementNumber }}<br>
                        <strong>System Class:</strong> 05 kWp Off-Grid / Hybrid Solar PV<br>
                        <strong>Tariff & Utility:</strong> CEB Non-Export / Off-Grid
                    </div>
                </td>
            </tr>
        </table>

        {{-- Parties to Agreement --}}
        <div class="section-title">1. Contracting Parties & Installation Site Details</div>
        <table class="parties-table">
            <tr>
                <td>
                    <div style="font-weight: 700; color: {{ $primaryColor }}; font-size: 9.5px; margin-bottom: 4px; text-transform: uppercase;">
                        The Client / Property Owner
                    </div>
                    <div style="font-size: 11px; font-weight: 800; color: #0f172a; margin-bottom: 2px;">
                        {{ $contact?->name ?? 'MR. M A N A M Fernando' }}
                    </div>
                    <div style="font-size: 8.5px; color: #334155; line-height: 1.4;">
                        <strong>NIC / Passport No:</strong> {{ $contact?->tax_no ?? 'NIC-651234567V' }}<br>
                        <strong>Site Address:</strong> {{ $contact?->address ?? '260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa' }}<br>
                        <strong>Contact Phone:</strong> {{ $contact?->phone ?? '+94 77 345 6789' }}<br>
                        <strong>CEB Account No:</strong> {{ $contact?->ceb_account_no ?? 'CEB-RU-199-005' }}
                    </div>
                </td>
                <td style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div style="font-weight: 700; color: {{ $primaryColor }}; font-size: 9.5px; margin-bottom: 4px; text-transform: uppercase;">
                        The Contractor / Service Provider
                    </div>
                    <div style="font-size: 11px; font-weight: 800; color: #0f172a; margin-bottom: 2px;">
                        {{ $company->name }}
                    </div>
                    <div style="font-size: 8.5px; color: #334155; line-height: 1.4;">
                        <strong>Authorized Rep:</strong> Managing Director & Technical Operations<br>
                        <strong>Office HQ:</strong> {{ $company->address ?? 'No. 33/A, Hapugahagedara, Loluwagoda, Mirigama' }}<br>
                        <strong>Hotline:</strong> {{ $company->phone ?? '+94 70 2 271 271 / +94 33 224 1241' }}<br>
                        <strong>Project Engineer:</strong> Lead Solar PV Systems Engineer
                    </div>
                </td>
            </tr>
        </table>

        {{-- System Technical Specifications --}}
        <div class="section-title">2. Certified Bill of Materials & System Engineering Specifications</div>
        <table class="spec-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 32%;">Component / Equipment Description</th>
                    <th style="width: 25%;">Model & Technical Specification</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 28%;">Manufacturer Warranty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><strong>Solar PV Panels</strong></td>
                    <td>AE 630W Bifacial Tier-1 High Output Modules (5.04 kWp Array)</td>
                    <td style="text-align: center; font-weight: 700;">8 Nos</td>
                    <td>30-Year Performance / 12-Yr Product</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><strong>Pure Sine Wave Inverter</strong></td>
                    <td>5.0 kW Off-Grid / Hybrid Smart Solar Inverter (48V DC, 230V AC)</td>
                    <td style="text-align: center; font-weight: 700;">1 No</td>
                    <td>10-Year Comprehensive Warranty</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><strong>Energy Storage Battery</strong></td>
                    <td>LiFePO4 Lithium Iron Phosphate 51.2V 100Ah (5.12 kWh Pack)</td>
                    <td style="text-align: center; font-weight: 700;">1 No</td>
                    <td>10-Year Cycle Life Warranty (6000+ Cyc)</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><strong>Solar Mounting Substructure</strong></td>
                    <td>Anodized Aluminum D Rail 04 with SS304 Tile Hooks & Clamps</td>
                    <td style="text-align: center; font-weight: 700;">4 Rails</td>
                    <td>10-Year Structural Integrity Warranty</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td><strong>DC Electrical Protection</strong></td>
                    <td>DC Breakers BB2-40 (40A 550V) + DC SPD BUD-40/2R (600V Surge)</td>
                    <td style="text-align: center; font-weight: 700;">2 Sets</td>
                    <td>05-Year Protection Warranty</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td><strong>AC Electrical Protection</strong></td>
                    <td>AC Surge Protector BUA-40/2 + ACL 40A Main Isolator Switch</td>
                    <td style="text-align: center; font-weight: 700;">1 Set</td>
                    <td>05-Year Switchgear Warranty</td>
                </tr>
                <tr>
                    <td>7</td>
                    <td><strong>DC Cabling & Earthing</strong></td>
                    <td>4mm² UV Lapp Solar DC Cable (35m Red + 35m Black) + 5Ft Copper Rod</td>
                    <td style="text-align: center; font-weight: 700;">70m / 1</td>
                    <td>10-Year Cable / Earth Safety Verified</td>
                </tr>
            </tbody>
        </table>

        {{-- Commercial Terms & Milestone Schedule --}}
        <div class="section-title">3. Turnkey Commercial Agreement & Milestone Payment Schedule</div>
        <table class="payment-table">
            <tr style="background: #f8fafc; font-weight: 700;">
                <td style="width: 50%;">Total Agreed Turnkey Investment (LKR)</td>
                <td style="width: 50%; text-align: right; font-size: 11px; font-weight: 800; color: {{ $primaryColor }};">
                    LKR {{ number_format($grandTotal, 2) }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Milestone 1 (50% Advance):</strong> Upon Contract Execution & Equipment Allocation<br>
                    <span style="font-size: 8px; color: #64748b;">Covers procurement of panels, battery, inverter, and structural hardware.</span>
                </td>
                <td style="text-align: right; font-weight: 700; color: #0f172a;">
                    LKR {{ number_format($milestone1, 2) }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Milestone 2 (50% Commissioning):</strong> Upon Mechanical & Electrical Commissioning<br>
                    <span style="font-size: 8px; color: #64748b;">Payable upon successful system startup, load testing, and client handover.</span>
                </td>
                <td style="text-align: right; font-weight: 700; color: #0f172a;">
                    LKR {{ number_format($milestone2, 2) }}
                </td>
            </tr>
        </table>

        {{-- Bank Wire Details --}}
        <div class="bank-box">
            <strong>Official Remittance Bank Accounts:</strong><br>
            • DFCC Bank — Account: <strong>101001490231</strong> (Branch: Giriulla) — VERTEX HOME SOLUTIONS (PVT) LTD<br>
            • Bank Of Ceylon (BOC) — Account: <strong>97194769</strong> (Branch: Giriulla) — VERTEX HOME SOLUTIONS (PVT) LTD
        </div>

        <div class="footer-bar">
            Page 1 of 2 — Solar System Installation Agreement & Specification — Vertex Home Solutions (Pvt) Ltd
        </div>
    </div>

    {{-- ==================== PAGE 2 ==================== --}}
    <div class="page-break">
        {{-- Header Minimal --}}
        <table style="width: 100%; border-collapse: collapse; border-bottom: 1.5px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 10px;">
            <tr>
                <td style="font-size: 9px; font-weight: 700; color: {{ $primaryColor }}; text-transform: uppercase;">
                    Vertex Home Solutions (Pvt) Ltd — Solar Installation Agreement
                </td>
                <td style="text-align: right; font-size: 9px; color: #64748b;">
                    Agreement Ref: <strong>{{ $agreementNumber }}</strong> | Client: <strong>{{ $contact?->name ?? 'MR. M A N A M Fernando' }}</strong>
                </td>
            </tr>
        </table>

        {{-- Scope of Work --}}
        <div class="section-title">4. Scope of Engineering & Installation Services</div>
        <div style="font-size: 8.5px; color: #334155; line-height: 1.45; margin-bottom: 6px;">
            Vertex Home Solutions (Pvt) Ltd guarantees complete turnkey delivery encompassing:
            <ol style="margin: 2px 0 6px 14px; padding: 0;">
                <li><strong>Site Engineering:</strong> Roof structural integrity survey, azimuth/tilt optimization, shadow trajectory simulation, and DC string voltage calculation.</li>
                <li><strong>Mechanical Installation:</strong> Precision mounting of 8x AE 630W modules using heavy-duty anodized aluminum rails with leak-proof roof penetration sealants.</li>
                <li><strong>Electrical & Battery Integration:</strong> Complete enclosure wiring with DC circuit breakers, class-II surge protectors, AC distribution switches, and 48V 100Ah battery bank coupling.</li>
                <li><strong>Safety Grounding:</strong> Driving and bonding of 5Ft copper-bonded earth rod, bonding all metallic frames and surge protective arrestors to exceed CEB safety criteria.</li>
                <li><strong>Testing & Commissioning:</strong> Open-circuit voltage (Voc) verification, short-circuit current (Isc) testing, insulation resistance testing, and live inverter load transition testing.</li>
                <li><strong>Handover & Documentation:</strong> User operation orientation, mobile monitoring app setup, safety placard placement, and delivery of official completion certification.</li>
            </ol>
        </div>

        {{-- Warranties & Guarantees --}}
        <div class="section-title">5. Comprehensive Equipment & Service Warranties</div>
        <div style="font-size: 8.5px; color: #334155; line-height: 1.45; margin-bottom: 6px;">
            All hardware components are Tier-1 certified and backed by official manufacturer guarantees:
            <ul style="margin: 2px 0 6px 14px; padding: 0;">
                <li><strong>Solar PV Modules (AE 630W):</strong> 30-Year Linear Power Performance Warranty guaranteeing not less than 84.8% nominal output at year 30, and 12-year manufacturer defects warranty.</li>
                <li><strong>Off-Grid / Hybrid Inverter (5kW):</strong> 10-Year Comprehensive Manufacturer Warranty covering logic motherboard, MPPT controller, and power electronics.</li>
                <li><strong>LiFePO4 Battery Storage (51.2V 100Ah):</strong> 10-Year Cycle Life Warranty (≥ 6,000 discharge cycles at 80% Depth of Discharge).</li>
                <li><strong>Workmanship & Service:</strong> 02-Year Comprehensive Free Workmanship & Site Callout Warranty by Vertex Home Solutions engineering team.</li>
            </ul>
        </div>

        {{-- Terms & Client Responsibilities --}}
        <div class="section-title">6. Terms, Client Responsibilities & Operational Conditions</div>
        <div style="font-size: 8px; color: #475569; line-height: 1.4; margin-bottom: 8px;">
            1. <strong>Site Access:</strong> The Client shall provide unhindered access to roof premises and electrical rooms during scheduled work hours (08:30 – 17:30).<br>
            2. <strong>Civil & Roof Condition:</strong> The Client confirms the underlying roof timber/purlin framing is structurally sound for standard module loads (approx. 18 kg/m²).<br>
            3. <strong>Title & Ownership:</strong> Equipment delivered to site remains under legal lien of Vertex Home Solutions (Pvt) Ltd until milestone payment obligations are settled.<br>
            4. <strong>Force Majeure:</strong> Neither party shall be liable for installation delays occasioned by severe weather storms, grid outages, or governmental acts beyond reasonable control.<br>
            5. <strong>Jurisdiction:</strong> This Agreement is governed by the laws of the Democratic Socialist Republic of Sri Lanka.
        </div>

        {{-- Signatures & Execution Block --}}
        <div class="section-title">7. Formal Contract Execution & Signatures</div>
        <table class="signature-table">
            <tr>
                <td>
                    <div style="font-size: 9px; font-weight: 700; color: {{ $primaryColor }}; text-transform: uppercase; margin-bottom: 2px;">
                        The Client / Purchaser
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-bottom: 6px;">
                        Accepted & agreed to all specifications & terms:
                    </div>
                    <div class="signature-line"></div>
                    <div style="font-size: 9px; font-weight: 700; color: #0f172a;">
                        {{ $contact?->name ?? 'MR. M A N A M Fernando' }}
                    </div>
                    <div style="font-size: 8px; color: #64748b;">
                        NIC: {{ $contact?->tax_no ?? 'NIC-651234567V' }}<br>
                        Date: ............................................
                    </div>
                </td>
                <td>
                    <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; margin-bottom: 2px;">
                        Witness / Sales Consultant
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-bottom: 6px;">
                        Project Facilitator & System Advisor:
                    </div>
                    <div class="signature-line"></div>
                    <div style="font-size: 9px; font-weight: 700; color: #0f172a;">
                        {{ $agentName ?? 'Krishantha (AGT-003)' }}
                    </div>
                    <div style="font-size: 8px; color: #64748b;">
                        Consultant Code: AGT-003<br>
                        Date: ............................................
                    </div>
                </td>
                <td>
                    <div style="font-size: 9px; font-weight: 700; color: {{ $primaryColor }}; text-transform: uppercase; margin-bottom: 2px;">
                        Contractor Signatory
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-bottom: 6px;">
                        For VERTEX HOME SOLUTIONS (PVT) LTD:
                    </div>
                    <div class="signature-line" style="position: relative;">
                        @if ($signaturePath)
                            <img src="{{ $signaturePath }}" style="max-height: 38px; position: absolute; bottom: 2px; left: 10px;" alt="Signature">
                        @endif
                    </div>
                    <div style="font-size: 9px; font-weight: 700; color: #0f172a;">
                        Managing Director
                    </div>
                    <div style="font-size: 8px; color: #64748b;">
                        Official Seal & Company Stamp<br>
                        Date: {{ $agreementDate }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-bar">
            Page 2 of 2 — Solar System Installation Agreement & Terms — Vertex Home Solutions (Pvt) Ltd
        </div>
    </div>

</body>
</html>
