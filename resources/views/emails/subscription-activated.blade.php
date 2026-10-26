<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Activated</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .wrapper { width: 100%; max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; color: #e0e7ff; font-size: 14px; }
        .content { padding: 32px 28px; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #dcfce7; color: #166534; margin-bottom: 20px; }
        .card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #cbd5e1; font-size: 14px; }
        .row:last-child { border-bottom: none; }
        .row-label { color: #64748b; font-weight: 500; }
        .row-value { color: #0f172a; font-weight: 600; text-align: right; }
        .btn-container { text-align: center; margin: 32px 0 16px; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .footer { background-color: #f1f5f9; padding: 20px 24px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>PixelCraft SaaS</h1>
            <p>Enterprise Business Suite</p>
        </div>
        <div class="content">
            <span class="badge">Active Subscription</span>
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Your Subscription Has Been Activated!</h2>
            <p style="font-size: 15px; line-height: 1.6; color: #334155;">
                Hello <strong>{{ $company->owner()?->name ?? 'Valued Customer' }}</strong>,<br>
                We are thrilled to inform you that your bank payment has been verified and approved by our team. Your company <strong>{{ $company->name }}</strong> now has full, uninterrupted access to all PixelCraft SaaS modules.
            </p>

            <div class="card">
                <div class="row">
                    <span class="row-label">Plan Tier</span>
                    <span class="row-value">{{ $payment->subscriptionPlan?->name ?? 'Professional Plan' }}</span>
                </div>
                <div class="row">
                    <span class="row-label">Paid Amount</span>
                    <span class="row-value">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="row">
                    <span class="row-label">Coverage Period</span>
                    <span class="row-value">{{ $payment->period_years }} Year(s)</span>
                </div>
                <div class="row">
                    <span class="row-label">Valid Until</span>
                    <span class="row-value">{{ $company->subscription_ends_at?->format('F d, Y') ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ url('/app/' . $company->slug) }}" class="btn">Launch Your Workspace &rarr;</a>
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                If you have any questions or require custom onboarding support, please contact our support team at <a href="mailto:support@pixelcraftlk.com" style="color: #4f46e5;">support@pixelcraftlk.com</a>.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PixelCraft LK. All rights reserved.<br>
            Secure Multi-Tenant Cloud ERP & Point of Sale System.
        </div>
    </div>
</body>
</html>
