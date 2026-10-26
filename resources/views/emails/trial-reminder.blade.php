<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Free Trial Reminder</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .wrapper { width: 100%; max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; color: #fef3c7; font-size: 14px; }
        .content { padding: 32px 28px; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #fef3c7; color: #92400e; margin-bottom: 20px; }
        .countdown { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin: 24px 0; text-align: center; }
        .countdown-number { font-size: 36px; font-weight: 800; color: #b45309; line-height: 1; }
        .countdown-label { font-size: 14px; font-weight: 600; color: #78350f; margin-top: 6px; }
        .btn-container { text-align: center; margin: 32px 0 16px; }
        .btn { display: inline-block; background-color: #f59e0b; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .footer { background-color: #f1f5f9; padding: 20px 24px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>PixelCraft SaaS</h1>
            <p>14-Day Free Trial Notice</p>
        </div>
        <div class="content">
            <span class="badge">Trial Ending Soon</span>
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Your Trial Expires in {{ $daysRemaining }} Day{{ $daysRemaining === 1 ? '' : 's' }}</h2>
            <p style="font-size: 15px; line-height: 1.6; color: #334155;">
                Hello <strong>{{ $company->owner()?->name ?? 'Valued Customer' }}</strong>,<br>
                We hope you have enjoyed exploring all the features of PixelCraft SaaS for <strong>{{ $company->name }}</strong>. Your 14-day free trial will conclude on <strong>{{ $company->trial_ends_at?->format('F d, Y') ?? 'soon' }}</strong>.
            </p>

            <div class="countdown">
                <div class="countdown-number">{{ $daysRemaining }}</div>
                <div class="countdown-label">Day{{ $daysRemaining === 1 ? '' : 's' }} Left in Free Trial</div>
            </div>

            <p style="font-size: 14px; line-height: 1.6; color: #334155;">
                To avoid any interruption to your invoicing, point of sale, payroll, and financial operations, please upgrade to an annual or monthly subscription by transferring payment and uploading your slip.
            </p>

            <div class="btn-container">
                <a href="{{ url('/app/' . $company->slug . '/billing') }}" class="btn">Upgrade Subscription Now &rarr;</a>
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                Have questions or need assistance? Our support team is ready to help at <a href="mailto:support@pixelcraftlk.com" style="color: #d97706;">support@pixelcraftlk.com</a>.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PixelCraft LK. All rights reserved.<br>
            Secure Multi-Tenant Cloud ERP & Point of Sale System.
        </div>
    </div>
</body>
</html>
