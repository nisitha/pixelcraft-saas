<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    @php
        $company = $company ?? $certificate->company;
        $paperSize = strtolower($certificate->paper_size ?? 'a4');
        $orientation = strtolower($certificate->orientation ?? 'landscape');

        $isA5 = $paperSize === 'a5';
        $isB5 = $paperSize === 'b5';
        $isPortrait = $orientation === 'portrait';

        // Scale factors for tight single-page layouts
        if ($isA5) {
            $margin = '8mm';
            $borderWidth = '2.5px';
            $innerPadding = $isPortrait ? '8px 10px' : '6px 12px';
            $titleSize = $isPortrait ? '14px' : '13px';
            $subTitleSize = '7px';
            $companyNameSize = '10px';
            $logoHeight = '30px';
            $customerNameSize = '13px';
            $proclamationSize = '7.5px';
            $tableHeaderSize = '7.5px';
            $tableFontSize = '7px';
            $tableCellPadding = '2.5px 5px';
            $sigTitleSize = '7.5px';
            $sigMetaSize = '6.5px';
            $sigSpacing = $isPortrait ? '14px' : '8px';
            $footerFontSize = '6.5px';
        } elseif ($isB5) {
            $margin = '10mm';
            $borderWidth = '3px';
            $innerPadding = $isPortrait ? '14px 16px' : '10px 18px';
            $titleSize = $isPortrait ? '18px' : '17px';
            $subTitleSize = '8.5px';
            $companyNameSize = '13px';
            $logoHeight = '42px';
            $customerNameSize = '16px';
            $proclamationSize = '9px';
            $tableHeaderSize = '9px';
            $tableFontSize = '8.5px';
            $tableCellPadding = '4px 7px';
            $sigTitleSize = '9px';
            $sigMetaSize = '8px';
            $sigSpacing = $isPortrait ? '18px' : '12px';
            $footerFontSize = '7.5px';
        } else {
            // A4 or Letter
            $margin = '10mm';
            $borderWidth = '4px';
            $innerPadding = $isPortrait ? '20px 22px' : '14px 24px';
            $titleSize = $isPortrait ? '21px' : '20px';
            $subTitleSize = '9.5px';
            $companyNameSize = '15px';
            $logoHeight = $isPortrait ? '50px' : '44px';
            $customerNameSize = $isPortrait ? '19px' : '18px';
            $proclamationSize = '10px';
            $tableHeaderSize = '10px';
            $tableFontSize = '9.5px';
            $tableCellPadding = $isPortrait ? '5px 8px' : '4px 8px';
            $sigTitleSize = '10px';
            $sigMetaSize = '8.5px';
            $sigSpacing = $isPortrait ? '24px' : '14px';
            $footerFontSize = '8px';
        }

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

        $logoPath = $resolveImagePath($company?->logo_path ?? ($company?->settings['logo_path'] ?? null));
        $signaturePath = $resolveImagePath($company?->settings['signature_path'] ?? null);
    @endphp

    <style>
        @page {
            size: {{ $paperSize }} {{ $orientation }};
            margin: {{ $margin }};
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.3;
            background-color: #ffffff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cert-outer-frame {
            border: {{ $borderWidth }} solid #0d5c75;
            padding: 4px;
            height: 100%;
            page-break-inside: avoid;
            background-color: #ffffff;
        }

        .cert-inner-frame {
            border: 1px solid #c59b27;
            padding: {{ $innerPadding }};
            height: 100%;
            position: relative;
        }

        /* Top Header */
        .cert-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .cert-header-table td {
            vertical-align: middle;
        }

        .company-name {
            font-size: {{ $companyNameSize }};
            font-weight: 800;
            color: #0d5c75;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0 0 2px 0;
        }

        .cert-main-title {
            font-family: 'Times New Roman', Times, Georgia, serif;
            font-size: {{ $titleSize }};
            font-weight: bold;
            color: #0d5c75;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin: 2px 0;
        }

        .cert-subtitle {
            font-size: {{ $subTitleSize }};
            font-weight: 700;
            color: #475569;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0;
        }

        .gold-divider {
            height: 2px;
            background: #c59b27;
            width: 100%;
            margin: 6px 0 8px 0;
            border: none;
        }

        /* Proclamation */
        .proclamation-wrap {
            text-align: center;
            margin: 6px 0 10px 0;
        }

        .proclamation-intro {
            font-style: italic;
            font-size: {{ $proclamationSize }};
            color: #475569;
            margin: 0 0 4px 0;
        }

        .customer-name {
            font-family: 'Times New Roman', Times, Georgia, serif;
            font-size: {{ $customerNameSize }};
            font-weight: bold;
            color: #0d5c75;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 2px 0 4px 0;
            border-bottom: 1.5px dotted #c59b27;
            display: inline-block;
            padding: 0 12px 2px 12px;
        }

        .proclamation-body {
            font-size: {{ $proclamationSize }};
            color: #334155;
            max-width: 92%;
            margin: 4px auto 0 auto;
            line-height: 1.35;
        }

        /* Specification Table */
        .spec-bar-header {
            background-color: #0d5c75;
            color: #ffffff;
            font-size: {{ $tableHeaderSize }};
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 6px;
            margin-top: 4px;
        }

        .spec-table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $tableFontSize }};
            margin-bottom: 8px;
        }

        .spec-table th, .spec-table td {
            border: 1px solid #cbd5e1;
            padding: {{ $tableCellPadding }};
            vertical-align: middle;
        }

        .spec-table .spec-label {
            background-color: #f8fafc;
            color: #0d5c75;
            font-weight: 700;
            width: 18%;
        }

        .spec-table .spec-value {
            color: #0f172a;
            font-weight: 600;
            width: 32%;
        }

        /* Signatures */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: {{ $sigSpacing }};
        }

        .sig-table td {
            vertical-align: bottom;
            text-align: center;
            width: 50%;
            padding: 0 20px;
        }

        .sig-line {
            border-top: 1.5px solid #0d5c75;
            width: 80%;
            margin: 0 auto 4px auto;
        }

        .sig-name {
            font-size: {{ $sigTitleSize }};
            font-weight: 800;
            color: #0d5c75;
            text-transform: uppercase;
            margin: 0;
        }

        .sig-role {
            font-size: {{ $sigMetaSize }};
            color: #64748b;
            margin: 1px 0;
            font-weight: 600;
        }

        .sig-date {
            font-size: {{ $sigMetaSize }};
            color: #0d5c75;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Bottom Footer */
        .cert-footer-wrap {
            margin-top: 8px;
            text-align: center;
        }

        .cert-footer-info {
            font-size: {{ $footerFontSize }};
            color: #64748b;
            margin: 3px 0 0 0;
        }

        .cert-official-badge {
            display: inline-block;
            font-style: italic;
            font-size: {{ $footerFontSize }};
            color: #0d5c75;
            font-weight: 700;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="cert-outer-frame">
    <div class="cert-inner-frame">

        {{-- Header: Logo & Title --}}
        <table class="cert-header-table">
            <tr>
                @if ($logoPath)
                    <td style="width: 20%; text-align: left;">
                        <img src="{{ $logoPath }}" style="max-height: {{ $logoHeight }}; max-width: 140px;" alt="Logo">
                    </td>
                    <td style="width: 80%; text-align: right; padding-right: 10px;">
                @else
                    <td style="width: 100%; text-align: center;">
                @endif
                    <div class="company-name">{{ $company->name ?? 'VERTEX HOME SOLUTIONS (PVT) LTD' }}</div>
                    <div class="cert-main-title">CERTIFICATE OF COMPLETION</div>
                    <div class="cert-subtitle">SOLAR POWER SYSTEM INSTALLATION & COMMISSIONING</div>
                </td>
            </tr>
        </table>

        <div class="gold-divider"></div>

        {{-- Proclamation Block --}}
        <div class="proclamation-wrap">
            <div class="proclamation-intro">
                This is to certify that the Solar PV Power System installed at the premises of
            </div>
            <div>
                <span class="customer-name">{{ $certificate->customer_name }}</span>
            </div>
            <div class="proclamation-body">
                has been successfully installed, thoroughly tested, and officially commissioned according to standard engineering and safety specifications.
            </div>
        </div>

        {{-- Specification Table --}}
        <div class="spec-bar-header">
            SYSTEM & INSTALLATION SPECIFICATIONS
        </div>
        <table class="spec-table">
            <tr>
                <td class="spec-label">System Capacity</td>
                <td class="spec-value">{{ $certificate->system_capacity }}</td>
                <td class="spec-label">Certificate No</td>
                <td class="spec-value" style="font-family: monospace; font-weight: 700; color: #0d5c75;">
                    {{ $certificate->certificate_number }}
                </td>
            </tr>
            <tr>
                <td class="spec-label">Site Address</td>
                <td class="spec-value">{{ $certificate->site_address }}</td>
                <td class="spec-label">Commissioning Date</td>
                <td class="spec-value">{{ $certificate->commissioning_date?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            <tr>
                <td class="spec-label">Grid Connection</td>
                <td class="spec-value">{{ $certificate->grid_connection }}</td>
                <td class="spec-label">Warranty Period</td>
                <td class="spec-value">{{ $certificate->warranty_period }}</td>
            </tr>
            <tr>
                <td class="spec-label">System Status</td>
                <td class="spec-value" style="color: #047857; font-weight: 700;">
                    ✓ {{ $certificate->system_status }}
                </td>
                <td class="spec-label">Verification</td>
                <td class="spec-value" style="color: #0d5c75; font-weight: 700;">
                    🛡 {{ $certificate->verification_status }}
                </td>
            </tr>
            <tr>
                <td class="spec-label">Compliance Note</td>
                <td class="spec-value" colspan="3" style="font-size: {{ $tableFontSize }}; color: #475569;">
                    {{ $certificate->compliance_note }}
                </td>
            </tr>
        </table>

        {{-- Dual Signature Section --}}
        <table class="sig-table">
            <tr>
                <td>
                    <div style="height: 32px;"></div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $certificate->lead_engineer_name }}</div>
                    <div class="sig-role">{{ $certificate->lead_engineer_title }}</div>
                    <div class="sig-date">Date: {{ $certificate->signed_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
                </td>
                <td>
                    @if ($signaturePath)
                        <div style="height: 32px; text-align: center;">
                            <img src="{{ $signaturePath }}" style="max-height: 32px; max-width: 120px;" alt="Signature">
                        </div>
                    @else
                        <div style="height: 32px;"></div>
                    @endif
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $certificate->director_name }}</div>
                    <div class="sig-role">{{ $certificate->director_title }}</div>
                    <div class="sig-date">Date: {{ $certificate->signed_date?->format('d/m/Y') ?? now()->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        {{-- Gold Divider & Bottom Footer --}}
        <div class="cert-footer-wrap">
            <div class="gold-divider" style="margin: 6px 0 4px 0;"></div>
            <div class="cert-footer-info">
                {{ $company?->address ?? '260/20, Negombo Road, Sri Lanka' }}
                @if ($company?->email) | Email: {{ $company->email }} @endif
                @if ($company?->phone) | Hotline: {{ $company->phone }} @endif
            </div>
            <div class="cert-official-badge">
                Official Solar Installation Completion & Commissioning Record
            </div>
        </div>

    </div>
</div>

</body>
</html>
