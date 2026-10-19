<x-filament-panels::page>
    <style>
        .vb-pos-root {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .vb-pos-root {
                flex-direction: row;
                align-items: flex-start;
            }
            .vb-pos-catalog {
                flex: 7;
                min-width: 0;
            }
            .vb-pos-sidebar {
                flex: 5;
                min-width: 330px;
                position: sticky;
                top: 4.5rem;
            }
        }
        .vb-product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
            gap: 0.875rem;
        }
        .vb-pos-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.875rem;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
            transition: all 0.15s ease-in-out;
        }
        .dark .vb-pos-card {
            background-color: #111827;
            border-color: #1f2937;
        }
        .vb-pos-card:hover {
            border-color: #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
            transform: translateY(-1px);
        }
        .vb-badge-in-stock {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .dark .vb-badge-in-stock {
            background-color: #064e3b;
            color: #a7f3d0;
            border-color: #047857;
        }
        .vb-badge-out-stock {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .dark .vb-badge-out-stock {
            background-color: #7f1d1d;
            color: #fecaca;
            border-color: #991b1b;
        }
        .vb-badge-serial {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
        }
        .dark .vb-badge-serial {
            background-color: #1e3a8a;
            color: #bfdbfe;
            border-color: #1d4ed8;
        }

        /* Dark Mode POS UI */
        .dark .vb-pos-card {
            background-color: #111827 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .dark .vb-pos-card h4 {
            color: #f8fafc !important;
        }
        .dark .vb-pos-card div[style*="border-top"] {
            border-top-color: rgba(255, 255, 255, 0.08) !important;
        }
        .dark input[type="text"], .dark select, .dark input[type="number"] {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
        }
        .dark div[style*="background-color: #f9fafb"], .dark div[style*="background-color: #ffffff"] {
            background-color: #111827 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }
        .dark div[style*="border-bottom: 1px solid #f3f4f6"] {
            border-bottom-color: rgba(255, 255, 255, 0.06) !important;
        }
        .dark div[style*="border-top: 1px solid #f3f4f6"] {
            border-top-color: rgba(255, 255, 255, 0.06) !important;
        }
        .dark span[style*="color: #111827"], .dark div[style*="color: #111827"], .dark label {
            color: #f8fafc !important;
        }
        .dark span[style*="color: #4b5563"], .dark div[style*="color: #4b5563"], .dark div[style*="color: #6b7280"] {
            color: #94a3b8 !important;
        }
        .dark button[style*="background-color: #f3f4f6"] {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #cbd5e1 !important;
        }
    </style>

    <div class="vb-pos-root">
        
        <!-- Left: Product Catalog & Search Filter -->
        <div class="vb-pos-catalog space-y-4">
            
            <!-- Search & Categories Bar -->
            <x-filament::section>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    
                    <!-- Search Input -->
                    <div style="position: relative; width: 100%;">
                        <div style="position: absolute; inset-block: 0; left: 0.75rem; display: flex; align-items: center; pointer-events: none; z-index: 10;">
                            <x-filament::icon 
                                icon="heroicon-o-magnifying-glass" 
                                style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #9ca3af;" 
                            />
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Search solar panels, inverters, SKU or service..." 
                            style="width: 100%; padding: 0.625rem 2.25rem 0.625rem 2.5rem; font-size: 0.875rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background-color: #f9fafb; outline: none;"
                        />
                        @if(!empty($search))
                            <button 
                                type="button"
                                wire:click="$set('search', '')" 
                                style="position: absolute; inset-block: 0; right: 0.75rem; display: flex; align-items: center; background: none; border: none; cursor: pointer; color: #9ca3af;"
                            >
                                <x-filament::icon icon="heroicon-o-x-mark" style="width: 1rem; height: 1rem; min-width: 1rem; min-height: 1rem;" />
                            </button>
                        @endif
                    </div>

                    <!-- Category Pills Filter -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.25rem;">
                        <button 
                            type="button"
                            wire:click="$set('selectedCategoryId', null)" 
                            style="padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; white-space: nowrap; transition: background 0.15s; {{ is_null($selectedCategoryId) ? 'background-color: #059669; color: #ffffff;' : 'background-color: #f3f4f6; color: #4b5563;' }}"
                        >
                            All Categories
                        </button>
                        @foreach($this->getCategories() as $cat)
                            <button 
                                type="button"
                                wire:click="$set('selectedCategoryId', {{ $cat->id }})" 
                                style="padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: none; cursor: pointer; white-space: nowrap; transition: background 0.15s; {{ $selectedCategoryId === $cat->id ? 'background-color: #059669; color: #ffffff;' : 'background-color: #f3f4f6; color: #4b5563;' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                </div>
            </x-filament::section>

            <!-- Product Grid -->
            <div class="vb-product-grid">
                @forelse($this->getProducts() as $product)
                    <div 
                        wire:click="addToCart({{ $product->id }})" 
                        class="vb-pos-card"
                    >
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.25rem; margin-bottom: 0.35rem;">
                                <span class="{{ $product->stock_qty > 0 || $product->type === 'service' ? 'vb-badge-in-stock' : 'vb-badge-out-stock' }}">
                                    {{ $product->type === 'service' ? 'Service' : 'Stock: ' . number_format((float) $product->stock_qty, 0) }}
                                </span>
                                @if($product->track_serial)
                                    <span class="vb-badge-serial">Serial</span>
                                @endif
                            </div>
                            <h4 style="font-size: 0.8rem; font-weight: 600; line-height: 1.35; margin: 0 0 0.25rem 0; color: #111827;">
                                {{ $product->name }}
                            </h4>
                            @if(!empty($product->sku))
                                <div style="font-size: 0.65rem; color: #6b7280; font-family: monospace;">SKU: {{ $product->sku }}</div>
                            @endif
                        </div>

                        <div style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: #059669;">
                                Rs. {{ number_format((float) $product->unit_price, 2) }}
                            </span>
                            <span style="width: 1.6rem; height: 1.6rem; border-radius: 9999px; background-color: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                                <x-filament::icon icon="heroicon-o-plus" style="width: 0.875rem; height: 0.875rem; min-width: 0.875rem; min-height: 0.875rem;" />
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 2.5rem; text-align: center; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; color: #9ca3af; font-size: 0.875rem;">
                        No products found matching your search.
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Right: Cart & Checkout Panel -->
        <div class="vb-pos-sidebar space-y-4">
            
            <x-filament::section heading="Counter Checkout">
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    
                    <!-- Customer Selector -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem;">
                            Customer / Account
                        </label>
                        <select 
                            wire:model.live="customerId" 
                            style="width: 100%; padding: 0.5rem 0.75rem; font-size: 0.8rem; border-radius: 0.5rem; border: 1px solid #d1d5db; background-color: #f9fafb; outline: none;"
                        >
                            @foreach($this->getCustomers() as $cust)
                                <option value="{{ $cust->id }}">
                                    {{ $cust->name }} {{ $cust->phone ? '('.$cust->phone.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cart Items Container -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                        
                        <div style="background-color: #f9fafb; padding: 0.5rem 0.75rem; font-size: 0.75rem; font-weight: 700; color: #4b5563; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb;">
                            <span>Cart ({{ count($cart) }} Items)</span>
                            @if(count($cart) > 0)
                                <button 
                                    type="button" 
                                    wire:click="clearCart" 
                                    style="background: none; border: none; cursor: pointer; color: #e11d48; font-size: 0.7rem; font-weight: 600;"
                                >
                                    Clear Cart
                                </button>
                            @endif
                        </div>

                        <div style="max-height: 220px; overflow-y: auto;">
                            @forelse($cart as $id => $item)
                                <div style="padding: 0.625rem 0.75rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; font-size: 0.75rem;">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item['name'] }}</div>
                                        <div style="font-size: 0.65rem; color: #6b7280;">Rs. {{ number_format($item['unit_price'], 2) }}</div>
                                    </div>

                                    <!-- Stepper -->
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <button 
                                            type="button"
                                            wire:click="decrementQuantity({{ $id }})" 
                                            style="width: 1.35rem; height: 1.35rem; border-radius: 0.25rem; background-color: #f3f4f6; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;"
                                        >-</button>
                                        <input 
                                            type="number" 
                                            value="{{ $item['quantity'] }}" 
                                            wire:change="updateQuantity({{ $id }}, $event.target.value)" 
                                            min="1"
                                            style="width: 2.25rem; text-align: center; padding: 0.15rem; font-size: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.25rem;"
                                        />
                                        <button 
                                            type="button"
                                            wire:click="incrementQuantity({{ $id }})" 
                                            style="width: 1.35rem; height: 1.35rem; border-radius: 0.25rem; background-color: #f3f4f6; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;"
                                        >+</button>
                                    </div>

                                    <!-- Line Total -->
                                    <div style="font-weight: 700; width: 4.5rem; text-align: right; color: #111827;">
                                        Rs. {{ number_format($item['subtotal'], 2) }}
                                    </div>

                                    <!-- Remove -->
                                    <button 
                                        type="button"
                                        wire:click="removeFromCart({{ $id }})" 
                                        style="background: none; border: none; cursor: pointer; color: #9ca3af; padding: 2px;"
                                    >
                                        <x-filament::icon icon="heroicon-o-trash" style="width: 1rem; height: 1rem; min-width: 1rem; min-height: 1rem; color: #ef4444;" />
                                    </button>
                                </div>
                            @empty
                                <div style="padding: 1.75rem 1rem; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                    Cart is empty. Click any product to add.
                                </div>
                            @endforelse
                        </div>

                    </div>

                    <!-- Financial Summary -->
                    <div style="display: flex; flex-direction: column; gap: 0.4rem; padding-top: 0.5rem; border-top: 1px solid #f3f4f6; font-size: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; color: #4b5563;">
                            <span>Subtotal:</span>
                            <span style="font-weight: 600; color: #111827;">Rs. {{ number_format($this->getSubtotal(), 2) }}</span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; color: #4b5563;">
                            <span>Discount (Rs.):</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                wire:model.live.debounce.400ms="discount" 
                                placeholder="0.00" 
                                style="width: 6rem; text-align: right; padding: 0.25rem 0.5rem; font-size: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;"
                            />
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; background-color: #ecfdf5; padding: 0.625rem 0.75rem; border-radius: 0.5rem; color: #065f46; font-size: 0.875rem; font-weight: 700; margin-top: 0.25rem;">
                            <span>Grand Total:</span>
                            <span>Rs. {{ number_format($this->getGrandTotal(), 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Method Selector -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem;">
                            Payment Method
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
                            @foreach(['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank'] as $key => $label)
                                <button 
                                    type="button" 
                                    wire:click="$set('paymentMethod', '{{ $key }}')" 
                                    style="padding: 0.5rem 0.25rem; font-size: 0.75rem; font-weight: 600; border-radius: 0.375rem; border: 1px solid #d1d5db; cursor: pointer; text-align: center; transition: all 0.15s; {{ $paymentMethod === $key ? 'background-color: #059669; color: #ffffff; border-color: #059669;' : 'background-color: #f9fafb; color: #374151;' }}"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Amount Tendered & Change -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; color: #6b7280; margin-bottom: 0.25rem;">Tendered (Rs.)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                wire:model.live.debounce.300ms="amountTendered" 
                                style="width: 100%; padding: 0.5rem; font-size: 0.8rem; font-weight: 600; border: 1px solid #d1d5db; border-radius: 0.375rem;"
                            />
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; color: #6b7280; margin-bottom: 0.25rem;">Change Due</label>
                            <div style="padding: 0.5rem; font-size: 0.8rem; font-weight: 700; background-color: #f3f4f6; border-radius: 0.375rem; color: #111827;">
                                Rs. {{ number_format($this->getChangeDue(), 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Complete Sale Button -->
                    <button 
                        type="button"
                        wire:click="completeSale" 
                        wire:loading.attr="disabled"
                        @disabled(empty($cart))
                        style="width: 100%; padding: 0.875rem 1rem; background-color: #059669; color: #ffffff; font-size: 0.875rem; font-weight: 700; border: none; border-radius: 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); {{ empty($cart) ? 'opacity: 0.5; cursor: not-allowed;' : '' }}"
                    >
                        <span wire:loading.remove style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <x-filament::icon icon="heroicon-o-printer" style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #ffffff;" />
                            Complete Sale & Print Receipt
                        </span>
                        <span wire:loading style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <x-filament::loading-indicator style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #ffffff;" />
                            Processing Checkout...
                        </span>
                    </button>

                </div>

            </x-filament::section>
            
        </div>

    </div>
</x-filament-panels::page>
