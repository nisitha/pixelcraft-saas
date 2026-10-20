@php
    $paperSize = strtolower($get('paper_size') ?? 'a4');
    $orientation = strtolower($get('orientation') ?? 'landscape');
    $status = strtolower($get('status') ?? 'draft');
    
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
    $company = \Filament\Facades\Filament::getTenant();

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

    $logoUrl = $resolveImage($company?->logo_path ?? ($company?->settings['logo_path'] ?? null));
    $signatureUrl = $resolveImage($company?->settings['signature_path'] ?? null);

    // Dimension definitions with real-world aspect ratios
    $dimensionsMap = [
        'a4' => [
            'name' => 'A4',
            'landscape' => ['w' => 1000, 'h' => 707, 'ratio' => '297 : 210', 'mm' => '297 × 210 mm'],
            'portrait' => ['w' => 707, 'h' => 1000, 'ratio' => '210 : 297', 'mm' => '210 × 297 mm'],
        ],
        'a5' => [
            'name' => 'A5',
            'landscape' => ['w' => 1000, 'h' => 705, 'ratio' => '210 : 148', 'mm' => '210 × 148 mm'],
            'portrait' => ['w' => 705, 'h' => 1000, 'ratio' => '148 : 210', 'mm' => '148 × 210 mm'],
        ],
        'letter' => [
            'name' => 'Letter',
            'landscape' => ['w' => 1000, 'h' => 773, 'ratio' => '11 : 8.5', 'mm' => '11 × 8.5 in'],
            'portrait' => ['w' => 773, 'h' => 1000, 'ratio' => '8.5 : 11', 'mm' => '8.5 × 11 in'],
        ],
        'b5' => [
            'name' => 'B5',
            'landscape' => ['w' => 1000, 'h' => 704, 'ratio' => '250 : 176', 'mm' => '250 × 176 mm'],
            'portrait' => ['w' => 704, 'h' => 1000, 'ratio' => '176 : 250', 'mm' => '176 × 250 mm'],
        ],
    ];

    $activeConfig = $dimensionsMap[$paperSize][$orientation] ?? $dimensionsMap['a4']['landscape'];
    $baseWidth = $activeConfig['w'];
    $baseHeight = $activeConfig['h'];
    $aspectRatioDisplay = $activeConfig['ratio'];
    $physicalDimensions = $activeConfig['mm'];

    $initialEstimatedHeight = $isPortrait ? 650 : 360;

    $canvasData = compact(
        'customerName', 'systemCapacity', 'certificateNumber', 'siteAddress',
        'commissioningDate', 'gridConnection', 'warrantyPeriod', 'systemStatus',
        'verificationStatus', 'complianceNote', 'leadEngineerName', 'leadEngineerTitle',
        'directorName', 'directorTitle', 'signedDate', 'isPortrait', 'company',
        'logoUrl', 'signatureUrl', 'paperSize', 'orientation', 'status'
    );
@endphp

<div
    x-data="{
        baseWidth: {{ $baseWidth }},
        baseHeight: {{ $baseHeight }},
        scale: 0.5,
        viewportHeight: {{ $initialEstimatedHeight }},
        isFullscreen: false,
        modalZoom: 1.0,

        init() {
            this.updateScale();
            this.$nextTick(() => {
                this.updateScale();
                if (window.ResizeObserver && this.$refs.previewViewport) {
                    const ro = new ResizeObserver(() => this.updateScale());
                    ro.observe(this.$refs.previewViewport);
                }
            });
        },

        updateScale() {
            if (!this.$refs.previewViewport) return;
            const w = this.$refs.previewViewport.clientWidth;
            if (w > 0 && this.baseWidth > 0) {
                this.scale = w / this.baseWidth;
                this.viewportHeight = Math.round(this.baseHeight * this.scale);
            }
        },

        openFullscreen() {
            this.isFullscreen = true;
            this.modalZoom = 1.0;
            document.body.classList.add('overflow-hidden');
        },

        closeFullscreen() {
            this.isFullscreen = false;
            document.body.classList.remove('overflow-hidden');
        },

        zoomIn() {
            this.modalZoom = Math.min(2.0, Math.round((this.modalZoom + 0.15) * 100) / 100);
        },

        zoomOut() {
            this.modalZoom = Math.max(0.4, Math.round((this.modalZoom - 0.15) * 100) / 100);
        },

        resetZoom() {
            this.modalZoom = 1.0;
        }
    }"
    wire:key="cert-preview-wrapper-{{ $paperSize }}-{{ $orientation }}"
    class="w-full cert-preview-card"
    style="position: -webkit-sticky; position: sticky; top: 1.5rem; align-self: flex-start; z-index: 20;"
>
    <style>
        .cert-preview-card svg {
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            max-width: 20px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>

    {{-- Executive SaaS Live Preview Container Card --}}
    <div class="rounded-2xl border border-slate-700/80 bg-slate-900 text-slate-100 shadow-2xl shadow-slate-950/40 overflow-hidden">
        
        {{-- Toolbar Top Row: Live Status & Technical Badges --}}
        <div class="p-3.5 bg-slate-950/80 border-b border-slate-800 flex flex-wrap items-center justify-between gap-2.5">
            {{-- Left: Live Pulse Indicator --}}
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-sm shadow-emerald-500"></span>
                </span>
                <span class="text-xs font-extrabold tracking-wider text-slate-100 uppercase">
                    Live Certificate Preview
                </span>
            </div>

            {{-- Right: High-Visibility Dimension Badges --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-black tracking-wide uppercase bg-teal-500/20 text-teal-300 border border-teal-500/40">
                    {{ strtoupper($paperSize) }} • {{ strtoupper($orientation) }}
                </span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-semibold text-slate-300 bg-slate-800/90 border border-slate-700">
                    {{ $aspectRatioDisplay }}
                </span>
                @if ($status === 'issued')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Issued
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        Draft
                    </span>
                @endif
            </div>
        </div>

        {{-- Toolbar Action Row: Fullscreen HD & Download PDF Direct Action --}}
        <div class="px-3.5 py-2.5 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between gap-2 flex-wrap">
            <div class="text-[11px] text-slate-400 font-medium hidden sm:block">
                Physical standard: <span class="text-slate-200 font-semibold">{{ $physicalDimensions }}</span>
            </div>

            <div class="flex items-center gap-2 ml-auto">
                {{-- Fullscreen HD Modal Trigger --}}
                <button
                    type="button"
                    @click="openFullscreen()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 active:bg-slate-600 text-slate-200 border border-slate-700 transition shadow-sm hover:text-white"
                    title="Inspect high-resolution full screen certificate"
                >
                    <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" class="text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span>Fullscreen Preview</span>
                </button>

                {{-- Direct PDF Download Button --}}
                <button
                    type="button"
                    wire:click="downloadPdf"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 hover:bg-teal-500 active:bg-teal-700 text-white transition shadow-sm disabled:opacity-50"
                    title="Download generated vector PDF"
                >
                    <svg wire:loading.remove wire:target="downloadPdf" style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <svg wire:loading wire:target="downloadPdf" style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" class="animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Download PDF</span>
                </button>
            </div>
        </div>

        {{-- Dynamic Scaled Viewport Area (Zero overlap, mathematically scaled to exact container width) --}}
        <div class="p-3 bg-slate-950 flex items-center justify-center overflow-hidden">
            <div
                x-ref="previewViewport"
                class="w-full relative overflow-hidden"
                :style="'height: ' + viewportHeight + 'px; min-height: ' + viewportHeight + 'px;'"
            >
                {{-- Inner Exact-Coordinate Certificate Sheet --}}
                <div
                    class="absolute top-0 left-0 bg-white shadow-2xl transition-transform duration-75 origin-top-left"
                    :style="'width: ' + baseWidth + 'px; height: ' + baseHeight + 'px; transform: scale(' + scale + ');'"
                >
                    @include('filament.app.resources.completion-certificates.certificate-canvas', $canvasData)
                </div>
            </div>
        </div>

        {{-- Footer Status Bar --}}
        <div class="px-3.5 py-2 bg-slate-950/90 border-t border-slate-800/80 text-[11px] text-slate-400 flex items-center justify-between">
            <div class="flex items-center gap-1.5 text-slate-300">
                <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" class="text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Vector accurate preview</span>
            </div>
            <div class="font-mono text-slate-400">
                {{ $certificateNumber ?: 'DRAFT' }}
            </div>
        </div>
    </div>

    {{-- Fullscreen High-Resolution Modal --}}
    <template x-teleport="body">
        <div
            x-show="isFullscreen"
            x-cloak
            @keydown.escape.window="closeFullscreen()"
            class="fixed inset-0 z-[99999] flex flex-col bg-slate-950/95 backdrop-blur-md text-slate-100"
        >
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-teal-400 shadow-sm shadow-teal-400"></div>
                    <div>
                        <div class="text-sm font-bold text-white flex items-center gap-2">
                            <span>Certificate Fullscreen View</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-slate-800 text-teal-300 border border-slate-700">
                                {{ $certificateNumber ?: 'DRAFT' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-400">
                            {{ strtoupper($paperSize) }} • {{ strtoupper($orientation) }} ({{ $physicalDimensions }})
                        </div>
                    </div>
                </div>

                {{-- Modal Center Controls: Zoom In / Out / Reset --}}
                <div class="flex items-center gap-1.5 bg-slate-800/80 border border-slate-700 rounded-lg p-1">
                    <button
                        type="button"
                        @click="zoomOut()"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded transition"
                        title="Zoom Out"
                    >
                        <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <span class="px-2 text-xs font-mono font-bold text-teal-300 min-w-[50px] text-center" x-text="Math.round(modalZoom * 100) + '%'">
                        100%
                    </span>
                    <button
                        type="button"
                        @click="zoomIn()"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded transition"
                        title="Zoom In"
                    >
                        <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="resetZoom()"
                        class="px-2 py-1 text-[11px] font-semibold text-slate-300 hover:text-white hover:bg-slate-700 rounded transition border-l border-slate-700 ml-1"
                    >
                        Reset 100%
                    </button>
                </div>

                {{-- Modal Right Actions: Print, Download, Close --}}
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition"
                    >
                        <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" class="text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print</span>
                    </button>

                    <button
                        type="button"
                        wire:click="downloadPdf"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 hover:bg-teal-500 text-white transition shadow-sm"
                    >
                        <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Download PDF</span>
                    </button>

                    <button
                        type="button"
                        @click="closeFullscreen()"
                        class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition"
                        title="Close Fullscreen (Esc)"
                    >
                        <svg style="width: 18px; height: 18px; min-width: 18px; max-width: 20px; display: inline-block; vertical-align: middle;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Modal Body: Scrollable Canvas Container --}}
            <div class="flex-1 overflow-auto p-6 md:p-12 flex justify-center items-start">
                <div
                    class="bg-white text-slate-900 shadow-2xl transition-transform origin-top duration-75"
                    :style="'width: ' + baseWidth + 'px; height: ' + baseHeight + 'px; transform: scale(' + modalZoom + ');'"
                >
                    @include('filament.app.resources.completion-certificates.certificate-canvas', $canvasData)
                </div>
            </div>
        </div>
    </template>
</div>
