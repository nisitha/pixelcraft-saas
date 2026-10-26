<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .wrapper { width: 100%; max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #475569 0%, #1e293b 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; color: #cbd5e1; font-size: 14px; }
        .content { padding: 32px 28px; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #fee2e2; color: #991b1b; margin-bottom: 20px; }
        .card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .btn-container { text-align: center; margin: 32px 0 16px; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .footer { background-color: #f1f5f9; padding: 20px 24px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>PixelCraft SaaS</h1>
            <p>Subscription Status Update</p>
        </div>
        <div class="content">
            <span class="badge">Workspace Inactive / Expired</span>
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">Your Workspace Access Has Expired</h2>
            <p style="font-size: 15px; line-height: 1.6; color: #334155;">
                Hello <strong>{{ $company->owner()?->name ?? 'Valued Customer' }}</strong>,<br>
                This is a notification that the subscription or free trial period for <strong>{{ $company->name }}</strong> has concluded. Your workspace is currently restricted from creating new transactions, documents, or data.
            </p>

            <div class="card">
                <p style="margin: 0; font-size: 14px; color: #475569; line-height: 1.6;">
                    <strong>Don't worry — your data is safe!</strong> All your invoices, customer records, inventory, and accounts remain securely preserved in your private database. Simply activate your plan to immediately resume normal business operations.
                </p>
            </div>

            <div class="btn-container">
                <a href="{{ url('/app/' . $company->slug . '/billing') }}" class="btn">Reactivate My Workspace Now &rarr;</a>
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                If you have recently transferred funds or have inquiries regarding your invoice, please contact our support team at <a href="mailto:billing@pixelcraftlk.com" style="color: #4f46e5;">billing@pixelcraftlk.com</a>.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PixelCraft LK. All rights reserved.<br>
            Secure Multi-Tenant Cloud ERP & Point of Sale System.
        </div>
    </div>
</body>
</html>
