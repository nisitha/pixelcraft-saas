<div 
    x-data="{ 
        open: @entangle('isOpen'),
        scrollToBottom() {
            $nextTick(() => {
                const ctn = this.$refs.chatContainer;
                if (ctn) {
                    ctn.scrollTop = ctn.scrollHeight;
                }
            });
        }
    }"
    x-init="$watch('$wire.messages', () => scrollToBottom()); $watch('open', value => { if (value) scrollToBottom(); })"
    class="relative"
>
    <!-- Floating Trigger Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button
            type="button"
            wire:click="toggleOpen"
            @click="open = !open; if(open) scrollToBottom()"
            class="relative flex items-center justify-center w-14 h-14 rounded-full text-white shadow-xl focus:outline-none transition-all duration-300 transform hover:scale-105 active:scale-95"
            style="background: linear-gradient(135deg, #4680ff 0%, #2f6bf0 100%); box-shadow: 0 8px 24px rgba(70, 128, 255, 0.38);"
            aria-label="Toggle Pixel Craft Studios AI Assistant"
        >
            <!-- Sparkles / Bot Icon -->
            <template x-if="!open">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8V4H8"></path>
                    <rect width="16" height="12" x="4" y="8" rx="2"></rect>
                    <path d="M2 14h2"></path>
                    <path d="M20 14h2"></path>
                    <path d="M15 13v2"></path>
                    <path d="M9 13v2"></path>
                </svg>
            </template>
            <template x-if="open">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </template>

            <!-- Subtle Pulse Glow Ring -->
            <span class="absolute -inset-1 rounded-full bg-blue-500 opacity-30 animate-ping pointer-events-none" style="animation-duration: 3s;"></span>

            <!-- Online Status Dot -->
            <span class="absolute top-0 right-0 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white dark:border-gray-900"></span>
            </span>
        </button>
    </div>

    <!-- Expanded Chat Window -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-250 transform"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-24 right-6 z-50 w-96 max-w-[calc(100vw-2rem)] h-[540px] max-h-[calc(100vh-8rem)] flex flex-col shadow-2xl rounded-2xl overflow-hidden border transition-all duration-200"
        style="border-radius: 16px; border: 1px solid #e8ebee; background: #ffffff;"
        :class="{ 'dark:bg-[#182230] dark:border-white/10': true }"
        x-cloak
    >
        <!-- Header -->
        <div class="px-4 py-3.5 flex items-center justify-between border-b" style="border-color: #e8ebee; background: linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);" :class="{ 'dark:bg-[#1e293b] dark:border-white/10': true }">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #4680ff 0%, #2f6bf0 100%);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2 2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
                        <rect x="3" y="8" width="18" height="12" rx="2"></rect>
                        <circle cx="9" cy="14" r="1.5" fill="currentColor"></circle>
                        <circle cx="15" cy="14" r="1.5" fill="currentColor"></circle>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold tracking-tight text-gray-900 dark:text-gray-100 leading-tight">
                        Pixel Craft Studios AI Assistant
                    </h3>
                    <div class="flex items-center space-x-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Vertex Books Intelligence</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-1">
                <!-- Clear Chat Action -->
                <button
                    type="button"
                    wire:click="clearChat"
                    title="Clear Chat History"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                        <path d="M21 3v5h-5"></path>
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                        <path d="M8 16H3v5"></path>
                    </svg>
                </button>

                <!-- Close / Minimize Action -->
                <button
                    type="button"
                    wire:click="toggleOpen"
                    @click="open = false"
                    title="Minimize"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div
            x-ref="chatContainer"
            class="flex-1 overflow-y-auto p-4 space-y-3.5 text-sm leading-relaxed"
            style="background: #ffffff;"
            :class="{ 'dark:bg-[#141d2b]': true }"
        >
            @foreach($messages as $msg)
                @if($msg['role'] === 'assistant')
                    <div class="flex items-start space-x-2.5 max-w-[92%]">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white flex-shrink-0 mt-0.5 shadow-xs" style="background: linear-gradient(135deg, #4680ff 0%, #2f6bf0 100%);">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2 2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
                                <rect x="3" y="8" width="18" height="12" rx="2"></rect>
                            </svg>
                        </div>
                        <div
                            class="p-3 rounded-2xl rounded-tl-xs shadow-xs text-xs space-y-1.5"
                            style="background: #f8f9fa; border: 1px solid #e8ebee; color: #1d2630;"
                            :class="{ 'dark:bg-[#1e293b] dark:border-white/10 dark:text-gray-200': true }"
                        >
                            <div class="prose prose-xs max-w-none dark:prose-invert break-words [&>p]:mb-1.5 [&>p:last-child]:mb-0 [&>table]:w-full [&>table]:text-[11px] [&>table]:border-collapse [&>table_th]:border [&>table_th]:p-1.5 [&>table_th]:bg-gray-100 dark:[&>table_th]:bg-gray-800 [&>table_td]:border [&>table_td]:p-1.5 [&>ul]:list-disc [&>ul]:pl-4 [&>ol]:list-decimal [&>ol]:pl-4">
                                {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-end justify-end">
                        <div
                            class="p-3 rounded-2xl rounded-tr-xs shadow-xs text-xs text-white max-w-[85%] break-words"
                            style="background: #4680ff;"
                        >
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Typing / Thinking State -->
            @if($isLoading)
                <div class="flex items-start space-x-2.5 max-w-[90%] animate-pulse">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white flex-shrink-0 mt-0.5" style="background: #4680ff;">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path>
                        </svg>
                    </div>
                    <div
                        class="p-3 rounded-2xl rounded-tl-xs shadow-xs flex items-center space-x-1.5"
                        style="background: #f8f9fa; border: 1px solid #e8ebee;"
                        :class="{ 'dark:bg-[#1e293b] dark:border-white/10': true }"
                    >
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 0ms;"></span>
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 150ms;"></span>
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 300ms;"></span>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 pl-1">Analyzing company books...</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Suggestion Pills -->
        <div class="px-3 py-2 border-t flex items-center gap-1.5 overflow-x-auto no-scrollbar" style="border-color: #f1f3f5; background: #fafbfc;" :class="{ 'dark:bg-[#182230] dark:border-white/5': true }">
            <button
                type="button"
                wire:click="sendMessage('Show me profit analysis for Mr. Fernando')"
                class="px-2.5 py-1 text-[11px] font-medium rounded-full border whitespace-nowrap transition-colors flex items-center gap-1 shadow-2xs hover:border-[#4680ff] hover:text-[#4680ff]"
                style="background: #ffffff; border-color: #e8ebee; color: #5b6b79;"
                :class="{ 'dark:bg-[#1e293b] dark:border-white/10 dark:text-gray-300': true }"
            >
                <span>⚡</span> Mr. Fernando Profit
            </button>
            <button
                type="button"
                wire:click="sendMessage('Show me project cost breakdown for Mr. Gamini')"
                class="px-2.5 py-1 text-[11px] font-medium rounded-full border whitespace-nowrap transition-colors flex items-center gap-1 shadow-2xs hover:border-[#4680ff] hover:text-[#4680ff]"
                style="background: #ffffff; border-color: #e8ebee; color: #5b6b79;"
                :class="{ 'dark:bg-[#1e293b] dark:border-white/10 dark:text-gray-300': true }"
            >
                <span>⚡</span> Mr. Gamini Breakdown
            </button>
            <button
                type="button"
                wire:click="sendMessage('List all unpaid and overdue invoices with balance dues')"
                class="px-2.5 py-1 text-[11px] font-medium rounded-full border whitespace-nowrap transition-colors flex items-center gap-1 shadow-2xs hover:border-[#4680ff] hover:text-[#4680ff]"
                style="background: #ffffff; border-color: #e8ebee; color: #5b6b79;"
                :class="{ 'dark:bg-[#1e293b] dark:border-white/10 dark:text-gray-300': true }"
            >
                <span>⚡</span> Unpaid Invoices
            </button>
        </div>

        <!-- Input Box -->
        <div class="p-3 border-t" style="border-color: #e8ebee; background: #ffffff;" :class="{ 'dark:bg-[#182230] dark:border-white/10': true }">
            <form wire:submit="sendMessage" class="flex items-center space-x-2">
                <input
                    type="text"
                    wire:model="input"
                    placeholder="Ask about profits, projects, inventory..."
                    class="flex-1 px-3 py-2 text-xs rounded-xl border focus:outline-none focus:ring-1 focus:ring-[#4680ff] focus:border-[#4680ff] transition-all bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100"
                    style="border-color: #e8ebee;"
                    :disabled="$wire.isLoading"
                />
                <button
                    type="submit"
                    :disabled="$wire.isLoading"
                    class="p-2 rounded-xl text-white shadow-xs focus:outline-none transition-all disabled:opacity-50 hover:opacity-90 active:scale-95 flex items-center justify-center flex-shrink-0"
                    style="background: #4680ff;"
                >
                    <svg class="w-4 h-4 transform rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
