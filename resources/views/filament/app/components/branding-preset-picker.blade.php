<div class="branding-preset-picker-wrapper py-2">
    <div class="mb-3">
        <label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-2">
            🎨 One-Click Designer Themes
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
            {{-- Emerald Solar --}}
            <button
                type="button"
                @click="applyPalette('#047857', '#047857', '#ffffff')"
                class="flex flex-col items-center gap-1.5 p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-emerald-500 hover:shadow-sm transition text-left cursor-pointer group"
                title="Emerald Solar"
            >
                <div class="flex items-center gap-1 w-full justify-center">
                    <span class="w-4 h-4 rounded-full bg-[#047857] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#064e3b] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#ffffff] border border-gray-300 shadow-xs"></span>
                </div>
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 group-hover:text-emerald-600">Emerald Solar</span>
            </button>

            {{-- Corporate Azure --}}
            <button
                type="button"
                @click="applyPalette('#2563eb', '#1e3a8a', '#ffffff')"
                class="flex flex-col items-center gap-1.5 p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-500 hover:shadow-sm transition text-left cursor-pointer group"
                title="Corporate Azure"
            >
                <div class="flex items-center gap-1 w-full justify-center">
                    <span class="w-4 h-4 rounded-full bg-[#2563eb] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#1e3a8a] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#ffffff] border border-gray-300 shadow-xs"></span>
                </div>
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 group-hover:text-blue-600">Corporate Azure</span>
            </button>

            {{-- Midnight Navy --}}
            <button
                type="button"
                @click="applyPalette('#0284c7', '#0f172a', '#ffffff')"
                class="flex flex-col items-center gap-1.5 p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-sky-500 hover:shadow-sm transition text-left cursor-pointer group"
                title="Midnight Navy"
            >
                <div class="flex items-center gap-1 w-full justify-center">
                    <span class="w-4 h-4 rounded-full bg-[#0284c7] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#0f172a] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#ffffff] border border-gray-300 shadow-xs"></span>
                </div>
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 group-hover:text-sky-600">Midnight Navy</span>
            </button>

            {{-- Amber Energy --}}
            <button
                type="button"
                @click="applyPalette('#d97706', '#78350f', '#ffffff')"
                class="flex flex-col items-center gap-1.5 p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-amber-500 hover:shadow-sm transition text-left cursor-pointer group"
                title="Amber Energy"
            >
                <div class="flex items-center gap-1 w-full justify-center">
                    <span class="w-4 h-4 rounded-full bg-[#d97706] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#78350f] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#ffffff] border border-gray-300 shadow-xs"></span>
                </div>
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 group-hover:text-amber-600">Amber Energy</span>
            </button>

            {{-- Modern Violet --}}
            <button
                type="button"
                @click="applyPalette('#7c3aed', '#4c1d95', '#ffffff')"
                class="flex flex-col items-center gap-1.5 p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-purple-500 hover:shadow-sm transition text-left cursor-pointer group"
                title="Modern Violet"
            >
                <div class="flex items-center gap-1 w-full justify-center">
                    <span class="w-4 h-4 rounded-full bg-[#7c3aed] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#4c1d95] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#ffffff] border border-gray-300 shadow-xs"></span>
                </div>
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 group-hover:text-purple-600">Modern Violet</span>
            </button>

            {{-- Carbon Minimal --}}
            <button
                type="button"
                @click="applyPalette('#18181b', '#27272a', '#ffffff')"
                class="flex flex-col items-center gap-1.5 p-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-500 hover:shadow-sm transition text-left cursor-pointer group"
                title="Carbon Minimal"
            >
                <div class="flex items-center gap-1 w-full justify-center">
                    <span class="w-4 h-4 rounded-full bg-[#18181b] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#27272a] shadow-xs"></span>
                    <span class="w-4 h-4 rounded-full bg-[#ffffff] border border-gray-300 shadow-xs"></span>
                </div>
                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Carbon Minimal</span>
            </button>
        </div>
    </div>
</div>
