{{-- Reusable High-Fidelity Certificate Canvas --}}
@php
    $isPortrait = ($orientation ?? 'landscape') === 'portrait';
    $paperSize = strtolower($paperSize ?? 'a4');
    $status = strtolower($status ?? 'draft');

    $resolveImage = function (?string $path): ?string {
        if (empty($path)) return null;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        if (file_exists(storage_path('app/public/' . $path))) {
            return asset('storage/' . $path);
        }
        return null;
    };

    $logoUrl = $logoUrl ?? $resolveImage($company?->logo_path ?? ($company?->settings['logo_path'] ?? null));
    $signatureUrl = $signatureUrl ?? $resolveImage($company?->settings['signature_path'] ?? null);
@endphp

<div style="width: 100%; height: 100%; background: #ffffff; color: #1e293b; padding: 6px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    {{-- Outer Teal Border Frame --}}
    <div style="border: 4px solid #0d5c75; padding: 3px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
        {{-- Inner Gold Border Frame --}}
        <div style="border: 1.5px solid #c59b27; padding: {{ $isPortrait ? '26px 26px' : '22px 28px' }}; height: 100%; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box; background: #ffffff;">
            
            {{-- TOP ZONE: Header, Divider, Proclamation --}}
            <div>
                {{-- Header Row: Logo & Company / Certificate Title --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                    <div>
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" style="max-height: {{ $isPortrait ? '52px' : '56px' }}; max-width: 170px; object-fit: contain;" alt="Company Logo">
                        @else
                            <div style="font-size: 13px; font-weight: 800; color: #0d5c75; border: 2px dashed #0d5c75; padding: 6px 14px; border-radius: 4px; letter-spacing: 1px; text-transform: uppercase;">
                                {{ $company->name ?? 'VERTEX SOLAR' }}
                            </div>
                        @endif
                    </div>

                    <div style="text-align: right; flex: 1;">
                        <div style="font-size: 13px; font-weight: 800; color: #0d5c75; text-transform: uppercase; letter-spacing: 0.8px;">
                            {{ $company->name ?? 'VERTEX HOME SOLUTIONS (PVT) LTD' }}
                        </div>
                        <div style="font-family: 'Times New Roman', Georgia, serif; font-size: {{ $isPortrait ? '24px' : '26px' }}; font-weight: 900; color: #0d5c75; letter-spacing: 1.5px; text-transform: uppercase; margin: 2px 0;">
                            CERTIFICATE OF COMPLETION
                        </div>
                        <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">
                            SOLAR POWER SYSTEM INSTALLATION & COMMISSIONING
                        </div>
                    </div>
                </div>

                {{-- Gold Accent Divider Line --}}
                <div style="height: 2px; background: #c59b27; width: 100%; margin: 8px 0 10px 0;"></div>

                {{-- Proclamation Block --}}
                <div style="text-align: center; margin: {{ $isPortrait ? '8px 0 12px 0' : '4px 0 8px 0' }};">
                    <div style="font-style: italic; font-size: 12.5px; color: #475569;">
                        This is to certify that the Solar PV Power System installed at the premises of
                    </div>
                    <div style="margin: 4px 0;">
                        <span style="font-family: 'Times New Roman', Georgia, serif; font-size: {{ $isPortrait ? '22px' : '23px' }}; font-weight: 800; color: #0d5c75; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px dotted #c59b27; padding: 0 16px 3px 16px; display: inline-block;">
                            {{ $customerName ?: 'MR. M A N A M FERNANDO' }}
                        </span>
                    </div>
                    <div style="font-size: 11px; color: #334155; max-width: 90%; margin: 3px auto 0 auto; line-height: 1.35;">
                        has been successfully installed, thoroughly tested, and officially commissioned according to standard engineering and safety specifications.
                    </div>
                </div>

                {{-- SYSTEM & INSTALLATION SPECIFICATIONS TABLE --}}
                <div style="background-color: #0d5c75; color: #ffffff; font-size: 11px; font-weight: 800; text-align: center; letter-spacing: 1.2px; text-transform: uppercase; padding: 5px 8px; margin-top: 6px; border-radius: 2px;">
                    SYSTEM & INSTALLATION SPECIFICATIONS
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 1px; table-layout: fixed;">
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700; width: 20%;">System Capacity</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #0f172a; font-weight: 600; width: 30%;">{{ $systemCapacity ?: '05 kW+05kw Battery offgrid Solar System' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700; width: 20%;">Certificate No</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; font-family: monospace; font-weight: 700; color: #0d5c75; width: 30%;">{{ $certificateNumber ?: 'CERT-VHE/2026/RU/199/005' }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Site Address</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #0f172a; font-weight: 600;">{{ $siteAddress ?: '260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Commissioning Date</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #0f172a; font-weight: 600;">{{ $commissioningDate ?: now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Grid Connection</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #0f172a; font-weight: 600;">{{ $gridConnection ?: 'On-Grid Net Metering' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Warranty Period</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #0f172a; font-weight: 600;">{{ $warrantyPeriod ?: '30 Years (Panels) / 10 Years (Inverter+Battery)' }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">System Status</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #047857; font-weight: 700;">✓ {{ $systemStatus ?: 'Commissioned & Operational' }}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Verification</td>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #0d5c75; font-weight: 700;">🛡 {{ $verificationStatus ?: 'Engineering Safety Compliant' }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 5px 9px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Compliance Note</td>
                        <td colspan="3" style="border: 1px solid #cbd5e1; padding: 5px 9px; color: #475569; font-size: 10.5px;">
                            {{ $complianceNote ?: 'Installed, tested, and commissioned in accordance with standard safety regulations.' }}
                        </td>
                    </tr>
                </table>
            </div>

            {{-- BOTTOM ZONE: Signatories, Gold Rule, Footer --}}
            <div style="margin-top: {{ $isPortrait ? '24px' : '16px' }};">
                {{-- Dual Signatures --}}
                <div style="display: flex; justify-content: space-around; text-align: center;">
                    <div style="width: 44%;">
                        <div style="height: 36px; display: flex; align-items: flex-end; justify-content: center;">
                            {{-- Placeholder space or seal --}}
                        </div>
                        <div style="border-top: 1.5px solid #0d5c75; width: 85%; margin: 0 auto 4px auto;"></div>
                        <div style="font-size: 11.5px; font-weight: 800; color: #0d5c75; text-transform: uppercase;">
                            {{ $leadEngineerName ?: 'Lead Engineer' }}
                        </div>
                        <div style="font-size: 10px; color: #64748b;">
                            {{ $leadEngineerTitle ?: 'Technical Operations' }}
                        </div>
                        <div style="font-size: 9.5px; color: #0d5c75; font-weight: 600;">
                            Date: {{ $signedDate ?: now()->format('d/m/Y') }}
                        </div>
                    </div>

                    <div style="width: 44%;">
                        <div style="height: 36px; display: flex; align-items: center; justify-content: center;">
                            @if ($signatureUrl)
                                <img src="{{ $signatureUrl }}" style="max-height: 36px; max-width: 130px; object-fit: contain;" alt="Authorized Signature">
                            @endif
                        </div>
                        <div style="border-top: 1.5px solid #0d5c75; width: 85%; margin: 0 auto 4px auto;"></div>
                        <div style="font-size: 11.5px; font-weight: 800; color: #0d5c75; text-transform: uppercase;">
                            {{ $directorName ?: 'Managing Director' }}
                        </div>
                        <div style="font-size: 10px; color: #64748b;">
                            {{ $directorTitle ?: 'Vertex Home Solutions (Pvt) Ltd' }}
                        </div>
                        <div style="font-size: 9.5px; color: #0d5c75; font-weight: 600;">
                            Date: {{ $signedDate ?: now()->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                {{-- Gold Rule & Company Address Footer --}}
                <div style="margin-top: 10px; text-align: center;">
                    <div style="height: 2px; background: #c59b27; width: 100%; margin: 6px 0 4px 0;"></div>
                    <div style="font-size: 9.5px; color: #64748b;">
                        {{ $company->address ?? '260/20, Negombo Road, Sri Lanka' }}
                        @if ($company?->email) | Email: {{ $company->email }} @endif
                        @if ($company?->phone) | Hotline: {{ $company->phone }} @endif
                    </div>
                    <div style="font-size: 9.5px; color: #0d5c75; font-style: italic; font-weight: 700; margin-top: 1px;">
                        Official Solar Installation Completion & Commissioning Record
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
