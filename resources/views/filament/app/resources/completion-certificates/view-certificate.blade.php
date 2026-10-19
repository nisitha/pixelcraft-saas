<x-filament-panels::page>
    @php
        $certificate = $this->record;
        $company = $certificate->company;
        $paperSize = strtolower($certificate->paper_size ?? 'a4');
        $orientation = strtolower($certificate->orientation ?? 'landscape');
        $isPortrait = $orientation === 'portrait';
        $isA5 = $paperSize === 'a5';
        $isB5 = $paperSize === 'b5';

        $aspectRatio = $isPortrait
            ? ($isA5 ? '148 / 210' : ($isB5 ? '176 / 250' : '210 / 297'))
            : ($isA5 ? '210 / 148' : ($isB5 ? '250 / 176' : '297 / 210'));

        $maxWidth = $isPortrait ? '700px' : '980px';

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
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.5rem; align-items: center;">

        {{-- Top Metadata Bar --}}
        <div style="width: 100%; max-width: {{ $maxWidth }}; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 800; font-size: 14px; color: #38bdf8;">
                    {{ $certificate->certificate_number }}
                </span>
                <span style="background: {{ $certificate->status === 'issued' ? '#059669' : '#64748b' }}; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                    {{ $certificate->status }}
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="background: rgba(255,255,255,0.1); color: #cbd5e1; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                    SIZE: {{ strtoupper($paperSize) }}
                </span>
                <span style="background: rgba(255,255,255,0.1); color: #cbd5e1; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                    LAYOUT: {{ strtoupper($orientation) }}
                </span>
                <button
                    type="button"
                    onclick="window.print()"
                    style="background: #3b82f6; color: #ffffff; border: none; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                >
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        {{-- Certificate Canvas --}}
        <div style="width: 100%; max-width: {{ $maxWidth }}; aspect-ratio: {{ $aspectRatio }}; background: #ffffff; color: #1e293b; border-radius: 6px; box-shadow: 0 15px 35px rgba(0,0,0,0.25); padding: 8px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
            
            {{-- Outer Teal Frame --}}
            <div style="border: 4px solid #0d5c75; padding: 4px; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;">
                {{-- Inner Gold Frame --}}
                <div style="border: 1px solid #c59b27; padding: {{ $isPortrait ? '24px 28px' : '18px 30px' }}; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; background: #ffffff;">
                    
                    {{-- Header Row --}}
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 14px;">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" style="max-height: {{ $isPortrait ? '48px' : '54px' }}; max-width: 160px; object-fit: contain;" alt="Logo">
                            @else
                                <div style="font-size: 13px; font-weight: 800; color: #0d5c75; border: 1.5px dashed #0d5c75; padding: 6px 12px; border-radius: 4px;">
                                    VERTEX SOLAR
                                </div>
                            @endif

                            <div style="text-align: right; flex: 1;">
                                <div style="font-size: 14px; font-weight: 800; color: #0d5c75; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $company->name ?? 'VERTEX HOME SOLUTIONS (PVT) LTD' }}
                                </div>
                                <div style="font-family: 'Times New Roman', serif; font-size: {{ $isPortrait ? '24px' : '22px' }}; font-weight: 900; color: #0d5c75; letter-spacing: 1.5px; text-transform: uppercase; margin: 2px 0;">
                                    CERTIFICATE OF COMPLETION
                                </div>
                                <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">
                                    SOLAR POWER SYSTEM INSTALLATION & COMMISSIONING
                                </div>
                            </div>
                        </div>

                        {{-- Gold Accent Line --}}
                        <div style="height: 2px; background: #c59b27; width: 100%; margin: 10px 0 12px 0;"></div>

                        {{-- Proclamation Block --}}
                        <div style="text-align: center; margin: 8px 0 12px 0;">
                            <div style="font-style: italic; font-size: 12px; color: #475569;">
                                This is to certify that the Solar PV Power System installed at the premises of
                            </div>
                            <div style="margin: 6px 0;">
                                <span style="font-family: 'Times New Roman', serif; font-size: {{ $isPortrait ? '22px' : '20px' }}; font-weight: 800; color: #0d5c75; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 2px dotted #c59b27; padding: 0 16px 4px 16px; display: inline-block;">
                                    {{ $certificate->customer_name }}
                                </span>
                            </div>
                            <div style="font-size: 11px; color: #334155; max-width: 90%; margin: 4px auto 0 auto; line-height: 1.4;">
                                has been successfully installed, thoroughly tested, and officially commissioned according to standard engineering and safety specifications.
                            </div>
                        </div>

                        {{-- Specifications Table --}}
                        <div style="background-color: #0d5c75; color: #ffffff; font-size: 11px; font-weight: 700; text-align: center; letter-spacing: 1px; text-transform: uppercase; padding: 5px 8px; margin-top: 6px;">
                            SYSTEM & INSTALLATION SPECIFICATIONS
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 1px;">
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700; width: 18%;">System Capacity</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #0f172a; font-weight: 600; width: 32%;">{{ $certificate->system_capacity }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700; width: 18%;">Certificate No</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; font-family: monospace; font-weight: 700; color: #0d5c75; width: 32%;">{{ $certificate->certificate_number }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Site Address</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #0f172a; font-weight: 600;">{{ $certificate->site_address }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Commissioning Date</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #0f172a; font-weight: 600;">{{ $certificate->commissioning_date?->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Grid Connection</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #0f172a; font-weight: 600;">{{ $certificate->grid_connection }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Warranty Period</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #0f172a; font-weight: 600;">{{ $certificate->warranty_period }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">System Status</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #047857; font-weight: 700;">✓ {{ $certificate->system_status }}</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Verification</td>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #0d5c75; font-weight: 700;">🛡 {{ $certificate->verification_status }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #cbd5e1; padding: 6px 10px; background: #f8fafc; color: #0d5c75; font-weight: 700;">Compliance Note</td>
                                <td colspan="3" style="border: 1px solid #cbd5e1; padding: 6px 10px; color: #475569; font-size: 10px;">
                                    {{ $certificate->compliance_note }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Bottom Zone: Signatures & Footer --}}
                    <div>
                        {{-- Dual Signatures --}}
                        <div style="display: flex; justify-content: space-around; margin-top: {{ $isPortrait ? '28px' : '20px' }}; text-align: center;">
                            <div style="width: 42%;">
                                <div style="height: 34px;"></div>
                                <div style="border-top: 1.5px solid #0d5c75; width: 85%; margin: 0 auto 4px auto;"></div>
                                <div style="font-size: 11px; font-weight: 800; color: #0d5c75; text-transform: uppercase;">{{ $certificate->lead_engineer_name }}</div>
                                <div style="font-size: 10px; color: #64748b;">{{ $certificate->lead_engineer_title }}</div>
                                <div style="font-size: 9.5px; color: #0d5c75; font-weight: 600;">Date: {{ $certificate->signed_date?->format('d/m/Y') }}</div>
                            </div>

                            <div style="width: 42%;">
                                @if ($signatureUrl)
                                    <div style="height: 34px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ $signatureUrl }}" style="max-height: 34px; max-width: 140px; object-fit: contain;" alt="Signature">
                                    </div>
                                @else
                                    <div style="height: 34px;"></div>
                                @endif
                                <div style="border-top: 1.5px solid #0d5c75; width: 85%; margin: 0 auto 4px auto;"></div>
                                <div style="font-size: 11px; font-weight: 800; color: #0d5c75; text-transform: uppercase;">{{ $certificate->director_name }}</div>
                                <div style="font-size: 10px; color: #64748b;">{{ $certificate->director_title }}</div>
                                <div style="font-size: 9.5px; color: #0d5c75; font-weight: 600;">Date: {{ $certificate->signed_date?->format('d/m/Y') }}</div>
                            </div>
                        </div>

                        {{-- Footer & Gold Rule --}}
                        <div style="margin-top: 10px; text-align: center;">
                            <div style="height: 2px; background: #c59b27; width: 100%; margin: 6px 0 4px 0;"></div>
                            <div style="font-size: 9.5px; color: #64748b;">
                                {{ $company->address ?? '260/20, Negombo Road, Sri Lanka' }}
                                @if ($company?->email) | Email: {{ $company->email }} @endif
                                @if ($company?->phone) | Hotline: {{ $company->phone }} @endif
                            </div>
                            <div style="font-size: 9.5px; color: #0d5c75; font-style: italic; font-weight: 700;">
                                Official Solar Installation Completion & Commissioning Record
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</x-filament-panels::page>
