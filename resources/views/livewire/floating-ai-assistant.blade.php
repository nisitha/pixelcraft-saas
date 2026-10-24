<div 
    x-data="{
        scrollToBottom() {
            $nextTick(() => {
                const el = this.$refs.chatContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        }
    }"
    x-init="$watch('$wire.messages', () => scrollToBottom()); if (window.Livewire) { Livewire.hook('commit', () => scrollToBottom()); }"
>
    <!-- Floating Trigger Button -->
    <div style="position: fixed !important; bottom: 25px !important; right: 25px !important; z-index: 999999 !important; display: block !important;">
        <button 
            type="button" 
            wire:click="toggleOpen"
            @click="scrollToBottom()"
            style="width: 60px !important; height: 60px !important; border-radius: 50% !important; background-color: #4680ff !important; box-shadow: 0 10px 25px rgba(70, 128, 255, 0.5) !important; border: 2px solid #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; position: relative !important; outline: none !important; padding: 0 !important;"
            title="Pixel Craft Studios AI Assistant"
        >
            <!-- Sparkles / Bot Icon -->
            <svg style="width: 28px !important; height: 28px !important; color: #ffffff !important; fill: none !important; stroke: #ffffff !important;" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
            </svg>
            <!-- Online Indicator Dot -->
            <span style="position: absolute !important; top: 2px !important; right: 2px !important; width: 14px !important; height: 14px !important; background-color: #10b981 !important; border: 2px solid #ffffff !important; border-radius: 50% !important;"></span>
        </button>
    </div>

    <!-- Expanded Chat Window -->
    @if($isOpen)
    <div style="position: fixed !important; bottom: 95px !important; right: 25px !important; width: 380px !important; max-width: calc(100vw - 32px) !important; height: 530px !important; max-height: calc(100vh - 120px) !important; background-color: #ffffff !important; border: 1px solid #e8ebee !important; border-radius: 16px !important; box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important; z-index: 999999 !important; display: flex !important; flex-direction: column !important; overflow: hidden !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;">
        <!-- Header -->
        <div style="background-color: #4680ff !important; color: #ffffff !important; padding: 14px 18px !important; display: flex !important; align-items: center !important; justify-content: space-between !important;">
            <div style="display: flex !important; align-items: center !important; gap: 10px !important;">
                <div style="width: 32px !important; height: 32px !important; background: rgba(255,255,255,0.2) !important; border-radius: 8px !important; display: flex !important; align-items: center !important; justify-content: center !important;">
                    🤖
                </div>
                <div>
                    <h4 style="margin: 0 !important; font-size: 14px !important; font-weight: 600 !important; color: #ffffff !important;">Pixel Craft Studios AI</h4>
                    <span style="font-size: 11px !important; opacity: 0.85 !important;">Gemini 2.0 Flash · Online</span>
                </div>
            </div>
            <button type="button" wire:click="toggleOpen" style="background: none !important; border: none !important; color: #ffffff !important; font-size: 20px !important; cursor: pointer !important; padding: 0 5px !important;">✕</button>
        </div>

        <!-- Chat History -->
        <div x-ref="chatContainer" style="flex: 1 !important; padding: 14px !important; overflow-y: auto !important; background-color: #f8f9fa !important; display: flex !important; flex-direction: column !important; gap: 12px !important;">
            @foreach($messages as $msg)
                <div style="display: flex !important; justify-content: {{ ($msg['role'] ?? '') === 'user' ? 'flex-end' : 'flex-start' }} !important;">
                    <div style="max-width: 82% !important; padding: 10px 14px !important; border-radius: 14px !important; font-size: 13px !important; line-height: 1.5 !important; {{ ($msg['role'] ?? '') === 'user' ? 'background-color: #4680ff !important; color: #ffffff !important;' : 'background-color: #ffffff !important; color: #1e293b !important; border: 1px solid #e2e8f0 !important;' }}">
                        {!! nl2br(e($msg['content'] ?? '')) !!}
                    </div>
                </div>
            @endforeach

            <!-- Instant Visual Loading Indicator -->
            <div wire:loading.flex wire:target="sendMessage" style="display: flex; align-items: center; gap: 8px; background: #ffffff; padding: 10px 14px; border-radius: 14px; border: 1px solid #e2e8f0; width: fit-content; margin-bottom: 8px;">
                <span style="font-size: 13px; color: #4680ff; font-weight: 500;">⚡ Calculating & analyzing project books...</span>
                <span style="font-size: 14px;">⏳</span>
            </div>
        </div>

        <!-- Input Box -->
        <form wire:submit.prevent="sendMessage" @submit="scrollToBottom()" style="padding: 10px 14px !important; background-color: #ffffff !important; border-top: 1px solid #e8ebee !important; display: flex !important; gap: 8px !important;">
            <input 
                type="text" 
                wire:model="input" 
                wire:loading.attr="disabled"
                wire:target="sendMessage"
                placeholder="Ask about projects, profit, bills..." 
                style="flex: 1 !important; padding: 10px 12px !important; border: 1px solid #cbd5e1 !important; border-radius: 8px !important; font-size: 13px !important; outline: none !important;"
            />
            <button 
                type="submit" 
                wire:loading.attr="disabled"
                wire:target="sendMessage"
                style="background-color: #4680ff !important; color: #ffffff !important; border: none !important; border-radius: 8px !important; padding: 0 16px !important; font-weight: 600 !important; font-size: 13px !important; cursor: pointer !important;"
            >
                Send
            </button>
        </form>
    </div>
    @endif
</div>
