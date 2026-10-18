<x-filament-panels::page>
    <style>
        .billing-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .billing-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        .stat-card-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            background: rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(148, 163, 184, 0.2);
            margin-bottom: 0.75rem;
        }
        .dark .stat-card-box {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .stat-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
        }
        .dark .stat-label {
            color: #94a3b8;
        }
        .stat-val {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
        }
        .dark .stat-val {
            color: #f8fafc;
        }
        .field-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.375rem;
            color: #334155;
        }
        .dark .field-label {
            color: #cbd5e1;
        }
        .field-input, .field-select, .field-textarea {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s ease;
        }
        .dark .field-input, .dark .field-select, .dark .field-textarea {
            border-color: #334155;
            background: #1e293b;
            color: #f8fafc;
        }
        .field-input:focus, .field-select:focus, .field-textarea:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }
        .field-help {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .dark .field-help {
            color: #94a3b8;
        }
        .bank-info-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.5rem;
            padding: 0.875rem 1rem;
            font-size: 0.8125rem;
            color: #166534;
            line-height: 1.5;
            margin-top: 1rem;
        }
        .dark .bank-info-box {
            background: rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.25);
            color: #6ee7b7;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
        }
        .history-table th {
            text-align: left;
            padding: 0.75rem 0.875rem;
            background: rgba(15, 23, 42, 0.03);
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .dark .history-table th {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }
        .history-table td {
            padding: 0.75rem 0.875rem;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .dark .history-table td {
            border-color: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
        }
    </style>

    @php
        $tenant = $this->getTenant();
        $isSubActive = $tenant ? $tenant->isSubscriptionActive() : false;
        $expiryDate = $tenant?->subscription_ends_at;
        $now = now();
        $daysRemaining = null;
        $isExpired = false;

        if ($expiryDate) {
            $isExpired = $expiryDate->isPast();
            $daysRemaining = $now->diffInDays($expiryDate, false);
        }

        $requests = $this->getSubscriptionRequests();
        $plans = $this->getAvailablePlans();
    @endphp

    <!-- Top Status Banner -->
    <div style="margin-bottom: 1.5rem;">
        @if(!$isSubActive)
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width: 1.5rem; height: 1.5rem; color: #ef4444; flex-shrink: 0;" />
                <div>
                    <strong style="color: #991b1b; font-size: 0.9375rem; display: block;">Subscription Suspended or Expired</strong>
                    <span style="color: #b91c1c; font-size: 0.8125rem;">
                        Your company workspace is currently in read-only mode. Submit a renewal request below with your bank transfer reference to reactivate all features immediately.
                    </span>
                </div>
            </div>
        @else
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.5rem; height: 1.5rem; color: #10b981; flex-shrink: 0;" />
                    <div>
                        <strong style="color: #065f46; font-size: 0.9375rem; display: block;">Subscription Active & In Good Standing</strong>
                        <span style="color: #047857; font-size: 0.8125rem;">
                            You have full access to Solar Sizing, Milestone Invoicing, POS Terminals, and Reports.
                        </span>
                    </div>
                </div>
                <x-filament::badge color="success" size="lg">Active</x-filament::badge>
            </div>
        @endif
    </div>

    <!-- 2-Column Overview and Renewal Form Grid -->
    <div class="billing-grid">
        <!-- Current Subscription Status Section -->
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-shield-check" style="width: 1.25rem; height: 1.25rem; color: #10b981;" />
                    <span>Current Subscription Status</span>
                </div>
            </x-slot>
            <x-slot name="description">
                Active plan tier, validity period, and bank transfer payment instructions.
            </x-slot>

            <div class="stat-card-box">
                <span class="stat-label">Subscribed Plan</span>
                <span class="stat-val" style="display: flex; align-items: center; gap: 0.5rem;">
                    {{ $tenant?->plan?->name ?? 'Free Trial' }}
                    @if($tenant?->plan)
                        <x-filament::badge color="info">Tier</x-filament::badge>
                    @endif
                </span>
            </div>

            <div class="stat-card-box">
                <span class="stat-label">Account Status</span>
                <div>
                    @if($isSubActive)
                        <x-filament::badge color="success">Active</x-filament::badge>
                    @else
                        <x-filament::badge color="danger">Expired / Inactive</x-filament::badge>
                    @endif
                </div>
            </div>

            <div class="stat-card-box">
                <span class="stat-label">Subscription Expiry Date</span>
                <span class="stat-val">
                    {{ $expiryDate ? $expiryDate->format('d M Y (h:i A)') : 'No Expiry Set' }}
                </span>
            </div>

            <div class="stat-card-box">
                <span class="stat-label">Time Remaining</span>
                <div>
                    @if($expiryDate)
                        @if($isExpired)
                            <x-filament::badge color="danger">
                                Expired {{ abs($daysRemaining) }} {{ Str::plural('day', abs($daysRemaining)) }} ago
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="warning">
                                {{ $daysRemaining }} {{ Str::plural('day', $daysRemaining) }} remaining
                            </x-filament::badge>
                        @endif
                    @else
                        <x-filament::badge color="gray">Indefinite</x-filament::badge>
                    @endif
                </div>
            </div>

            <!-- Official Bank Remittance Box -->
            <div class="bank-info-box">
                <div style="font-weight: 700; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.375rem;">
                    <x-filament::icon icon="heroicon-o-building-library" style="width: 1.1rem; height: 1.1rem;" />
                    <span>Vertex Innovation LK — Bank Transfer Account Details:</span>
                </div>
                <div><strong>Bank:</strong> Bank of Ceylon (BOC) / Commercial Bank PLC</div>
                <div><strong>Account Name:</strong> Vertex Innovation LK (Pvt) Ltd</div>
                <div><strong>Account Number:</strong> 0089-2049-1829-01</div>
                <div><strong>Branch:</strong> Corporate Branch, Colombo</div>
                <div style="margin-top: 0.375rem; font-style: italic; font-size: 0.75rem;">
                    * After transferring, please enter your deposit slip/reference code in the renewal form on the right.
                </div>
            </div>
        </x-filament::section>

        <!-- Renewal Request Form Section -->
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-arrow-path" style="width: 1.25rem; height: 1.25rem; color: #10b981;" />
                    <span>Request Subscription Renewal</span>
                </div>
            </x-slot>
            <x-slot name="description">
                Submit your bank transfer proof for Super Admin verification and instant extension.
            </x-slot>

            <form wire:submit.prevent="submitRenewalRequest" style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- Plan Selection -->
                <div>
                    <label class="field-label" for="plan_id">Choose Plan Tier</label>
                    <select wire:model.live="plan_id" id="plan_id" class="field-select">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }} (Rs. {{ number_format($plan->price_monthly, 0) }}/mo • Rs. {{ number_format($plan->price_yearly, 0) }}/yr)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Billing Cycle -->
                <div>
                    <label class="field-label" for="billing_cycle">Billing Cycle</label>
                    <select wire:model.live="billing_cycle" id="billing_cycle" class="field-select">
                        <option value="monthly">Monthly Cycle (1 Month Extension)</option>
                        <option value="yearly">Yearly Cycle (12 Months Extension — Save ~17%)</option>
                    </select>
                </div>

                <!-- Calculated Amount Preview -->
                <div style="background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.3); border-radius: 0.5rem; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 0.8125rem; font-weight: 600; color: #047857;">Total Payable (LKR):</span>
                    <span style="font-size: 1.25rem; font-weight: 800; color: #047857;">
                        Rs. {{ number_format($this->getEstimatedAmount(), 2) }}
                    </span>
                </div>

                <!-- Bank Reference Number -->
                <div>
                    <label class="field-label" for="payment_reference">Bank Transfer / Slip Reference No <span style="color: #ef4444;">*</span></label>
                    <input wire:model="payment_reference" id="payment_reference" type="text" class="field-input" placeholder="e.g. BOC-TXN-9028194 or SLIP-884920" required />
                    @error('payment_reference')
                        <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <div class="field-help">Enter the online banking transaction ID, slip number, or check number.</div>
                </div>

                <!-- Optional Notes -->
                <div>
                    <label class="field-label" for="notes">Additional Remarks (Optional)</label>
                    <textarea wire:model="notes" id="notes" class="field-textarea" rows="2" placeholder="e.g. Paid via online banking from Commercial Bank personal account"></textarea>
                </div>

                <!-- Submit Button -->
                <div style="margin-top: 0.5rem;">
                    <x-filament::button type="submit" size="lg" color="success" style="width: 100%;">
                        Submit Renewal for Verification
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>

    <!-- Past Subscription Requests Table -->
    <div style="margin-top: 1.5rem;">
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-filament::icon icon="heroicon-o-clock" style="width: 1.25rem; height: 1.25rem; color: #10b981;" />
                    <span>Renewal Request History</span>
                </div>
            </x-slot>
            <x-slot name="description">
                List of all renewal requests submitted for {{ $tenant?->name }}.
            </x-slot>

            <div style="overflow-x: auto;">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th># Ref</th>
                            <th>Plan Tier</th>
                            <th>Cycle</th>
                            <th>Amount (LKR)</th>
                            <th>Reference No</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th>Verified By / Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td style="font-weight: 600;">REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td><strong>{{ $req->plan?->name }}</strong></td>
                                <td style="text-transform: capitalize;">{{ $req->billing_cycle }}</td>
                                <td style="font-weight: 700; color: #047857;">Rs. {{ number_format($req->amount, 2) }}</td>
                                <td style="font-family: monospace;">{{ $req->payment_reference }}</td>
                                <td>{{ $req->created_at ? $req->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    @if($req->status === 'approved')
                                        <x-filament::badge color="success">Approved</x-filament::badge>
                                    @elseif($req->status === 'rejected')
                                        <x-filament::badge color="danger">Rejected</x-filament::badge>
                                    @else
                                        <x-filament::badge color="warning">Pending Verification</x-filament::badge>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'approved')
                                        <span style="font-size: 0.75rem;">
                                            {{ $req->approver?->name ?? 'Super Admin' }}<br>
                                            <span style="color: #64748b;">{{ $req->approved_at ? $req->approved_at->format('d/m/Y') : '' }}</span>
                                        </span>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Awaiting Review</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                    No subscription renewal requests submitted yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
