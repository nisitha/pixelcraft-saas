@php
    $paperSize = strtolower($get('paper_size') ?? 'a4');
    $orientation = strtolower($get('orientation') ?? 'landscape');
    $customerName = $get('customer_name') ?: 'MR. M A N A M FERNANDO';
    $systemCapacity = $get('system_capacity') ?: '05 kW+05kw Battery offgrid Solar System';
    $certificateNumber = $get('certificate_number') ?: 'CERT-VHE/2026/RU/199/005';
    $siteAddress = $get('site_address') ?: '260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa';
    $commissioningDate = $get('commissioning_date') ? \Carbon\Carbon::parse($get('commissioning_date'))->format('d/m/Y') : now()->format('d/m/Y');
    $gridConnection = $get('grid_connection') ?: 'On-Grid Net Metering';
    $warrantyPeriod = $get('warranty_period') ?: '30 Years (Panels) / 10 Years (Inverter+Battery)';
    $systemStatus = $get('system_status') ?: 'Commissioned & Operational';
    $verificationStatus = $get('verification_status') ?: 'Engineering Safety Compliant';
    $complianceNote = $get('compliance_note') ?: 'Installed, tested, and commissioned in accordance with standard safety regulations.';
    $leadEngineerName = $get('lead_engineer_name') ?: 'Lead Engineer';
    $leadEngineerTitle = $get('lead_engineer_title') ?: 'Technical Operations';
    $directorName = $get('director_name') ?: 'Managing Director';
    $directorTitle = $get('director_title') ?: 'Vertex Home Solutions (Pvt) Ltd';
    $signedDate = $get('signed_date') ? \Carbon\Carbon::parse($get('signed_date'))->format('d/m/Y') : now()->format('d/m/Y');

    $isPortrait = $orientation === 'portrait';
    $isA5 = $paperSize === 'a5';
    $isB5 = $paperSize === 'b5';

    $company = \Filament\Facades\Filament::getTenant();

    $resolveImagePath = function (?string $path): ?string {
        if (empty($path)) return null;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        return null;
    };

    $logoUrl = $resolveImagePath($company?->logo_path ?? ($company?->settings['logo_path'] ?? null));
    $signatureUrl = $resolveImagePath($company?->settings['signature_path'] ?? null);

    // Dynamic aspect ratios & max widths for preview
    if ($isPortrait) {
        $aspectRatio = $isA5 ? '148 / 210' : ($isB5 ? '176 / 250' : '210 / 297');
        $maxWidth = '520px';
        $titleSize = $isA5 ? '13px' : '17px';
        $customerSize = $isA5 ? '13px' : '16px';
        $subSize = $isA5 ? '7px' : '9px';
        $bodySize = $isA5 ? '7.5px' : '9.5px';
        $cellSize = $isA5 ? '2px 4px' : '4px 6px';
        $sigMargin = $isA5 ? '10px' : '18px';
    } else {
        $aspectRatio = $isA5 ? '210 / 148' : ($isB5 ? '250 / 176' : '297 / 210');
        $maxWidth = '100%';
        $titleSize = $isA5 ? '14px' : '19px';
        $customerSize = $isA5 ? '14px' : '18px';
        $subSize = $isA5 ? '8px' : '9.5px';
        $bodySize = $isA5 ? '8px' : '10px';
        $cellSize = $isA5 ? '3px 5px' : '4px 7px';
        $sigMargin = $isA5 ? '8px' : '14px';
    }
@endphp

<div class="vb-cert-preview-card" style="background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 16px; color: #f8fafc; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
    {{-- Top Info Bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px #10b981;"></span>
            <span style="font-weight: 700; font-size: 13px; letter-spacing: 0.5px; text-transform: uppercase; color: #38bdf8;">
                Live Certificate Preview
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; font-size: 11px;">
            <span style="background: #0f766e; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">
                {{ strtoupper($paperSize) }}
            </span>
            <span style="background: #0284c7; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">
                {{ strtoupper($orientation) }}
            </span>
            <span style="background: rgba(255,255,255,0.1); color: #cbd5e1; padding: 2px 8px; border-radius: 4px;">
                Ratio: {{ $aspectRatio }}
            </span>
        </div>
    </div>

    {{-- Certificate Visual Box --}}
    <div style="display: flex; justify-content: center; overflow-x: auto; padding: 8px 0;">
        <div style="width: 100%; max-width: {{ $maxWidth }}; aspect-ratio: {{ $aspectRatio }}; background: #ffffff; color: #1e293b; border-radius: 4px; box-shadow: 0 8px 24px rgba(0,0,0,0.35); padding: 6px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; overflow: hidden;">
            
            {{-- Outer Teal Frame --}}
            <div style="border: 3.5px solid #0d5c75; padding: 3px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                {{-- Inner Gold Frame --}}
                <div style="border: 1px solid #c59b27; padding: 12px 16px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; background: #ffffff;">
                    
                    {{-- Header Row --}}
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" style="max-height: {{ $isPortrait ? '36px' : '42px' }}; max-width: 120px; object-fit: contain;" alt="Logo">
                            @else
                                <div style="font-size: 10px; font-weight: 800; color: #0d5c75; border: 1px dashed #0d5c75; padding: 4px 8px; border-radius: 4px;">
                                    VERTEX SOLAR
                                </div>
                            @endif

                            <div style="text-align: right; flex: 1;">
                                <div style="font-size: 11px; font-weight: 800; color: #0d5c75; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $company->name ?? 'VERTEX HOME SOLUTIONS (PVT) LTD' }}
                                </div>
                                <div style="font-family: 'Times New Roman', serif; font-size: {{ $titleSize }}; font-weight: 900; color: #0d5c75; letter-spacing: 1px; text-transform: uppercase; margin: 1px 0;">
                                    CERTIFICATE OF COMPLETION
                                </div>
                                <div style="font-size: {{ $subSize }}; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                                    SOLAR POWER SYSTEM INSTALLATION & COMMISSIONING
                                </div>
                            </div>
                        </div>

                        {{-- Gold Accent Line --}}
                        <div style="height: 2px; background: #c59b27; width: 100%; margin: 6px 0;"></div>

                        {{-- Proclamation Block --}}
                        <div style="text-align: center; margin: 4px 0 6px 0;">
                            <div style="font-style: italic; font-size: {{ $bodySize }}; color: #475569;">
                                This is to certify that the Solar PV Power System installed at the premises of
                            </div>
                            <div style="margin: 3px 0;">
                                <span style="font-family: 'Times New Roman', serif; font-size: {{ $customerSize }}; font-weight: 800; color: #0d5c75; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px dotted #c59b27; padding: 0 10px 2px 10px; display: inline-block;">
                                    {{ $customerName }}
                                </span>
                            </div>
                            <div style="font-size: {{ $bodySize }}; color: #334155; max-width: 92%; margin: 2px auto 0 auto; line-height: 1.3;">
                                has been successfully installed, thoroughly tested, and officially commissioned according to standard engineering and safety specifications.
                            </div>
                        </div>

                        {{-- Specifications Table --}}
                        <div style="background-color: #0d5c75; color: #ffffff; font-size: {{ $subSize }}; font-weight: 700; text-align: center; letter-spacing: 0.8px; text-transform: uppercase; padding: 3px 6px; margin-top: 4px;">
                            SYSTEM & INSTALLATION SPECIFICATIONS
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: {{ $bodySize }}; margin-top: 1px;">
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700; width: 18%;">System Capacity</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #0f172a; font-weight: 600; width: 32%;">{{ $systemCapacity }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700; width: 18%;">Certificate No</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; font-family: monospace; font-weight: 700; color: #0d5c75; width: 32%;">{{ $certificateNumber }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">Site Address</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #0f172a; font-weight: 600;">{{ $siteAddress }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">Commissioning Date</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #0f172a; font-weight: 600;">{{ $commissioningDate }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">Grid Connection</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #0f172a; font-weight: 600;">{{ $gridConnection }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">Warranty Period</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #0f172a; font-weight: 600;">{{ $warrantyPeriod }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">System Status</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #047857; font-weight: 700;">✓ {{ $systemStatus }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">Verification</td>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #0d5c75; font-weight: 700;">🛡 {{ $verificationStatus }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; background: #f8fafc; color: #0d5c75; font-weight: 700;">Compliance Note</td>
                                <td colspan="3" style="border: 1px solid #cbd5e1; padding: {{ $cellSize }}; color: #475569; font-size: {{ $subSize }};">
                                    {{ $complianceNote }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Bottom Zone: Signatures & Footer --}}
                    <div>
                        {{-- Dual Signatures --}}
                        <div style="display: flex; justify-content: space-around; margin-top: {{ $sigMargin }}; text-align: center;">
                            <div style="width: 42%;">
                                <div style="height: 24px;"></div>
                                <div style="border-top: 1.5px solid #0d5c75; width: 85%; margin: 0 auto 3px auto;"></div>
                                <div style="font-size: {{ $subSize }}; font-weight: 800; color: #0d5c75; text-transform: uppercase;">{{ $leadEngineerName }}</div>
                                <div style="font-size: {{ $subSize }}; color: #64748b;">{{ $leadEngineerTitle }}</div>
                                <div style="font-size: {{ $subSize }}; color: #0d5c75; font-weight: 600;">Date: {{ $signedDate }}</div>
                            </div>

                            <div style="width: 42%;">
                                @if ($signatureUrl)
                                    <div style="height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ $signatureUrl }}" style="max-height: 24px; max-width: 100px; object-fit: contain;" alt="Signature">
                                    </div>
                                @else
                                    <div style="height: 24px;"></div>
                                @endif
                                <div style="border-top: 1.5px solid #0d5c75; width: 85%; margin: 0 auto 3px auto;"></div>
                                <div style="font-size: {{ $subSize }}; font-weight: 800; color: #0d5c75; text-transform: uppercase;">{{ $directorName }}</div>
                                <div style="font-size: {{ $subSize }}; color: #64748b;">{{ $directorTitle }}</div>
                                <div style="font-size: {{ $subSize }}; color: #0d5c75; font-weight: 600;">Date: {{ $signedDate }}</div>
                            </div>
                        </div>

                        {{-- Footer & Gold Rule --}}
                        <div style="margin-top: 6px; text-align: center;">
                            <div style="height: 1.5px; background: #c59b27; width: 100%; margin: 4px 0 3px 0;"></div>
                            <div style="font-size: {{ $subSize }}; color: #64748b;">
                                {{ $company->address ?? '260/20, Negombo Road, Sri Lanka' }}
                                @if ($company?->email) | Email: {{ $company->email }} @endif
                                @if ($company?->phone) | Hotline: {{ $company->phone }} @endif
                            </div>
                            <div style="font-size: {{ $subSize }}; color: #0d5c75; font-style: italic; font-weight: 700;">
                                Official Solar Installation Completion & Commissioning Record
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
