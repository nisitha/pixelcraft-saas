<x-filament-panels::page>
    @php
        $certificate = $this->record;
        $company = $certificate->company;
        $paperSize = strtolower($certificate->paper_size ?? 'a4');
        $orientation = strtolower($certificate->orientation ?? 'landscape');
        $isPortrait = $orientation === 'portrait';

        $dimensionsMap = [
            'a4' => [
                'landscape' => ['w' => 1000, 'h' => 707, 'ratio' => '297 / 210', 'mm' => '297 × 210 mm'],
                'portrait' => ['w' => 707, 'h' => 1000, 'ratio' => '210 / 297', 'mm' => '210 × 297 mm'],
            ],
            'a5' => [
                'landscape' => ['w' => 1000, 'h' => 705, 'ratio' => '210 / 148', 'mm' => '210 × 148 mm'],
                'portrait' => ['w' => 705, 'h' => 1000, 'ratio' => '148 / 210', 'mm' => '148 × 210 mm'],
            ],
            'letter' => [
                'landscape' => ['w' => 1000, 'h' => 773, 'ratio' => '11 / 8.5', 'mm' => '11 × 8.5 in'],
                'portrait' => ['w' => 773, 'h' => 1000, 'ratio' => '8.5 / 11', 'mm' => '8.5 × 11 in'],
            ],
            'b5' => [
                'landscape' => ['w' => 1000, 'h' => 704, 'ratio' => '250 / 176', 'mm' => '250 × 176 mm'],
                'portrait' => ['w' => 704, 'h' => 1000, 'ratio' => '176 / 250', 'mm' => '176 × 250 mm'],
            ],
        ];

        $activeConfig = $dimensionsMap[$paperSize][$orientation] ?? $dimensionsMap['a4']['landscape'];
        $baseWidth = $activeConfig['w'];
        $baseHeight = $activeConfig['h'];
        $aspectRatioStr = $activeConfig['ratio'];
        $physicalDimensions = $activeConfig['mm'];

        $resolveImagePath = function (?string $path): ?string {
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

        $logoUrl = $resolveImagePath($company?->logo_path ?? ($company?->settings['logo_path'] ?? null));
        $signatureUrl = $resolveImagePath($company?->settings['signature_path'] ?? null);

        $customerName = $certificate->customer_name;
        $systemCapacity = $certificate->system_capacity;
        $certificateNumber = $certificate->certificate_number;
        $siteAddress = $certificate->site_address;
        $commissioningDate = $certificate->commissioning_date?->format('d/m/Y');
        $gridConnection = $certificate->grid_connection;
        $warrantyPeriod = $certificate->warranty_period;
        $systemStatus = $certificate->system_status;
        $verificationStatus = $certificate->verification_status;
        $complianceNote = $certificate->compliance_note;
        $leadEngineerName = $certificate->lead_engineer_name;
        $leadEngineerTitle = $certificate->lead_engineer_title;
        $directorName = $certificate->director_name;
        $directorTitle = $certificate->director_title;
        $signedDate = $certificate->signed_date?->format('d/m/Y');
        $status = $certificate->status;

        $canvasData = compact(
            'customerName', 'systemCapacity', 'certificateNumber', 'siteAddress',
            'commissioningDate', 'gridConnection', 'warrantyPeriod', 'systemStatus',
            'verificationStatus', 'complianceNote', 'leadEngineerName', 'leadEngineerTitle',
            'directorName', 'directorTitle', 'signedDate', 'isPortrait', 'company',
            'logoUrl', 'signatureUrl', 'paperSize', 'orientation', 'status'
        );
    @endphp

    <div class="flex flex-col gap-6 items-center w-full max-w-6xl mx-auto">

        {{-- Top Metadata Bar --}}
        <div class="w-full bg-slate-900 border border-slate-800 rounded-xl p-4 flex items-center justify-between flex-wrap gap-4 text-slate-100 shadow-lg">
            <div class="flex items-center gap-3">
                <span class="font-extrabold text-base text-teal-400 font-mono tracking-wider">
                    {{ $certificate->certificate_number }}
                </span>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold uppercase {{ $certificate->status === 'issued' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700 text-slate-300' }}">
                    {{ $certificate->status }}
                </span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 border border-slate-700 text-xs font-semibold">
                    {{ strtoupper($paperSize) }} • {{ strtoupper($orientation) }}
                </span>
                <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-400 border border-slate-700 text-xs font-mono">
                    {{ $aspectRatioStr }}
                </span>
                <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-400 border border-slate-700 text-xs">
                    {{ $physicalDimensions }}
                </span>
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold border border-slate-700 transition"
                >
                    <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" class="text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Print</span>
                </button>
            </div>
        </div>

        {{-- Dynamic Scaled Certificate Container --}}
        <div
            x-data="{
                baseWidth: {{ $baseWidth }},
                baseHeight: {{ $baseHeight }},
                scale: 1.0,
                viewportHeight: {{ $baseHeight }},

                init() {
                    this.updateScale();
                    this.$nextTick(() => {
                        this.updateScale();
                        if (window.ResizeObserver && this.$refs.viewBox) {
                            new ResizeObserver(() => this.updateScale()).observe(this.$refs.viewBox);
                        }
                    });
                },

                updateScale() {
                    if (!this.$refs.viewBox) return;
                    const w = this.$refs.viewBox.clientWidth;
                    if (w > 0 && this.baseWidth > 0) {
                        this.scale = Math.min(1.0, w / this.baseWidth);
                        this.viewportHeight = Math.round(this.baseHeight * this.scale);
                    }
                }
            }"
            x-ref="viewBox"
            class="w-full flex justify-center overflow-hidden"
            :style="'height: ' + viewportHeight + 'px;'"
        >
            <div
                class="origin-top bg-white shadow-2xl rounded-sm transition-transform duration-75"
                :style="'width: ' + baseWidth + 'px; height: ' + baseHeight + 'px; transform: scale(' + scale + ');'"
            >
                @include('filament.app.resources.completion-certificates.certificate-canvas', $canvasData)
            </div>
        </div>

    </div>
</x-filament-panels::page>
