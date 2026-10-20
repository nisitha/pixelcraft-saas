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
        scale: {{ $isPortrait ? 0.55 : 0.42 }},
        isFullscreen: false,
        modalZoom: 1.0,

        init() {
            this.updateScale();
            this.$nextTick(() => {
                this.updateScale();
            });
            if (window.ResizeObserver && this.$refs.studioFrame) {
                this.ro = new ResizeObserver(() => {
                    this.updateScale();
                });
                this.ro.observe(this.$refs.studioFrame);
            }
            window.addEventListener('resize', () => this.updateScale());
        },

        updateScale() {
            let w = this.$refs.studioFrame ? this.$refs.studioFrame.clientWidth : 0;
            if (!w && this.$el) {
                w = Math.max(0, this.$el.clientWidth - 40);
            }
            if (w > 0 && this.baseWidth > 0) {
                this.scale = w / this.baseWidth;
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
            this.modalZoom = Math.min(2.5, Math.round((this.modalZoom + 0.15) * 100) / 100);
        },

        zoomOut() {
            this.modalZoom = Math.max(0.4, Math.round((this.modalZoom - 0.15) * 100) / 100);
        },

        resetZoom() {
            this.modalZoom = 1.0;
        }
    }"
    wire:key="cert-preview-studio-{{ $paperSize }}-{{ $orientation }}"
    class="w-full"
>
    <style>
        /* Responsive Two-Column Able Pro Layout: 55% Form / 45% Sticky Preview Studio */
        @media (min-width: 1024px) {
            .able-pro-cert-studio-layout {
                display: flex !important;
                flex-direction: row !important;
                gap: 1.5rem !important;
                align-items: flex-start !important;
            }
            .able-pro-form-col {
                flex: 0 0 55% !important;
                max-width: 55% !important;
                width: 55% !important;
            }
            .able-pro-preview-col {
                flex: 0 0 calc(45% - 1.5rem) !important;
                max-width: calc(45% - 1.5rem) !important;
                width: calc(45% - 1.5rem) !important;
                position: -webkit-sticky !important;
                position: sticky !important;
                top: 1.5rem !important;
                align-self: flex-start !important;
                z-index: 20;
            }
        }

        /* Left Column Form Inputs: Sleek Able Pro Cards */
        .able-pro-form-col .fi-section,
        .able-pro-card {
            background: #ffffff !important;
            border: 1px solid #e8ebee !important;
            border-radius: 12px !important;
            padding: 20px !important;
            box-shadow: 0 2px 10px rgba(33, 40, 50, 0.04) !important;
            margin-bottom: 1.25rem !important;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .able-pro-card:hover {
            box-shadow: 0 4px 16px rgba(33, 40, 50, 0.08) !important;
        }
        .dark .able-pro-form-col .fi-section,
        .dark .able-pro-card {
            background: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25) !important;
        }

        /* Form Inputs Able Pro Focus Borders */
        .able-pro-form-col input:focus,
        .able-pro-form-col select:focus,
        .able-pro-form-col textarea:focus,
        .able-pro-form-col .fi-input:focus,
        .able-pro-form-col .fi-select-input:focus,
        .able-pro-form-col .fi-input-wrp:focus-within {
            border-color: #4680ff !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(70, 128, 255, 0.2) !important;
            --tw-ring-color: rgba(70, 128, 255, 0.2) !important;
        }

        /* Dedicated Able Pro Live Preview Studio (Right Column) */
        .able-pro-studio-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            position: sticky;
            top: 1.5rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        }
        .dark .able-pro-studio-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }

        /* Certificate Paper Frame with Realistic Shadow */
        .able-pro-paper-frame {
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
            border-radius: 4px;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        .dark .able-pro-paper-frame {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.6);
        }

        /* Able Pro Royal Blue Button */
        .able-pro-btn-primary {
            background-color: #4680ff !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(70, 128, 255, 0.35) !important;
            transition: all 0.2s ease !important;
        }
        .able-pro-btn-primary:hover {
            background-color: #3b74ee !important;
            box-shadow: 0 4px 12px rgba(70, 128, 255, 0.45) !important;
        }
        .able-pro-btn-primary:active {
            transform: translateY(1px);
        }
    </style>

    {{-- Dedicated Able Pro Live Preview Studio Card --}}
    <div class="able-pro-studio-card">
        {{-- Top Header Bar --}}
        <div class="flex items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-200/80 dark:border-slate-800">
            {{-- Left: Live Studio badge (● LIVE PREVIEW in emerald green) + Paper size pill (A4 • LANDSCAPE) --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    LIVE PREVIEW
                </span>

                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase bg-white text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 shadow-sm">
                    {{ strtoupper($paperSize) }} • {{ strtoupper($orientation) }}
                </span>
            </div>

            {{-- Right: Action buttons --}}
            <div class="flex items-center gap-2">
                {{-- Fullscreen Modal Preview (heroicon-o-arrows-pointing-out) --}}
                <button
                    type="button"
                    @click="openFullscreen()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-slate-900 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700 transition shadow-sm"
                    title="Fullscreen Modal Preview"
                >
                    <x-filament::icon
                        icon="heroicon-o-arrows-pointing-out"
                        style="width: 1rem; height: 1rem; min-width: 1rem; min-height: 1rem; display: inline-block;"
                    />
                    <span class="hidden sm:inline">Fullscreen</span>
                </button>

                {{-- Download PDF button in Able Pro Royal Blue (#4680ff) --}}
                <button
                    type="button"
                    wire:click="downloadPdf"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg able-pro-btn-primary disabled:opacity-50"
                    title="Download PDF Certificate"
                >
                    <x-filament::icon
                        wire:loading.remove
                        wire:target="downloadPdf"
                        icon="heroicon-o-arrow-down-tray"
                        style="width: 1rem; height: 1rem; min-width: 1rem; min-height: 1rem; display: inline-block;"
                    />
                    <svg
                        wire:loading
                        wire:target="downloadPdf"
                        class="animate-spin text-white"
                        style="width: 1rem; height: 1rem; min-width: 1rem; min-height: 1rem; display: inline-block;"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Download PDF</span>
                </button>
            </div>
        </div>

        {{-- Certificate Paper Frame Stage (Centered perfectly using flexbox) --}}
        <div class="flex items-center justify-center w-full py-1">
            <div
                x-ref="studioFrame"
                class="able-pro-paper-frame w-full"
                :style="'aspect-ratio: ' + baseWidth + ' / ' + baseHeight + ';'"
            >
                {{-- Centered Scaled Sheet --}}
                <div
                    class="absolute top-0 left-0 bg-white origin-top-left"
                    :style="'width: ' + baseWidth + 'px; height: ' + baseHeight + 'px; transform: scale(' + scale + '); -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;'"
                >
                    @include('filament.app.resources.completion-certificates.certificate-canvas', $canvasData)
                </div>
            </div>
        </div>

        {{-- Studio Footer Meta Bar --}}
        <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-1.5 font-medium">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Vector accurate canvas</span>
            </div>
            <div class="text-[11px] font-mono text-slate-400 dark:text-slate-500">
                {{ $aspectRatioDisplay }} &bull; {{ $physicalDimensions }}
            </div>
        </div>
    </div>

    {{-- Fullscreen High-Resolution Modal Preview --}}
    <template x-teleport="body">
        <div
            x-show="isFullscreen"
            x-cloak
            @keydown.escape.window="closeFullscreen()"
            class="fixed inset-0 z-[99999] flex flex-col bg-slate-950/95 backdrop-blur-md text-slate-100"
        >
            {{-- Modal Top Navigation Bar --}}
            <div class="px-6 py-3.5 bg-slate-900 border-b border-slate-800 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <div>
                        <div class="text-sm font-bold text-white flex items-center gap-2">
                            <span>Completion Certificate Preview</span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-slate-800 text-sky-400 border border-slate-700">
                                {{ $certificateNumber ?: 'DRAFT' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-400">
                            {{ strtoupper($paperSize) }} • {{ strtoupper($orientation) }} ({{ $physicalDimensions }})
                        </div>
                    </div>
                </div>

                {{-- Zoom Controls --}}
                <div class="flex items-center gap-1.5 bg-slate-800/90 border border-slate-700 rounded-lg p-1">
                    <button
                        type="button"
                        @click="zoomOut()"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded transition"
                        title="Zoom Out"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <span class="px-2 text-xs font-mono font-bold text-sky-400 min-w-[50px] text-center" x-text="Math.round(modalZoom * 100) + '%'">
                        100%
                    </span>
                    <button
                        type="button"
                        @click="zoomIn()"
                        class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded transition"
                        title="Zoom In"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="resetZoom()"
                        class="px-2.5 py-1 text-[11px] font-medium text-slate-300 hover:text-white hover:bg-slate-700 rounded transition border-l border-slate-700 ml-1"
                    >
                        Reset
                    </button>
                </div>

                {{-- Modal Actions --}}
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition"
                    >
                        <x-filament::icon icon="heroicon-o-printer" style="width: 1rem; height: 1rem;" />
                        <span>Print</span>
                    </button>

                    <button
                        type="button"
                        wire:click="downloadPdf"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg able-pro-btn-primary"
                    >
                        <x-filament::icon wire:loading.remove wire:target="downloadPdf" icon="heroicon-o-arrow-down-tray" style="width: 1rem; height: 1rem;" />
                        <span>Download PDF</span>
                    </button>

                    <button
                        type="button"
                        @click="closeFullscreen()"
                        class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition"
                        title="Close Fullscreen (Esc)"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
