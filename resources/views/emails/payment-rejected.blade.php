<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .wrapper { width: 100%; max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; color: #ffe4e6; font-size: 14px; }
        .content { padding: 32px 28px; }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 600; background-color: #ffe4e6; color: #9f1239; margin-bottom: 20px; }
        .card { background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 20px; margin: 24px 0; }
        .reason-box { background-color: #ffffff; border: 1px dashed #f43f5e; border-radius: 6px; padding: 14px; margin-top: 12px; color: #881337; font-weight: 500; font-size: 14px; }
        .btn-container { text-align: center; margin: 32px 0 16px; }
        .btn { display: inline-block; background-color: #e11d48; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .footer { background-color: #f1f5f9; padding: 20px 24px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>PixelCraft SaaS</h1>
            <p>Payment Verification Notice</p>
        </div>
        <div class="content">
            <span class="badge">Payment Verification Issue</span>
            <h2 style="margin-top: 0; color: #0f172a; font-size: 20px;">We Couldn't Verify Your Bank Payment</h2>
            <p style="font-size: 15px; line-height: 1.6; color: #334155;">
                Hello <strong>{{ $company->owner()?->name ?? 'Valued Customer' }}</strong>,<br>
                Thank you for submitting your payment slip for <strong>{{ $company->name }}</strong>. Unfortunately, our finance team was unable to verify the bank transfer submission.
            </p>

            <div class="card">
                <div style="font-weight: 600; color: #9f1239; font-size: 14px;">Reason for Rejection:</div>
                <div class="reason-box">
                    {{ $rejectionReason }}
                </div>
            </div>

            <p style="font-size: 14px; line-height: 1.6; color: #334155;">
                Please re-check your transfer reference, ensure the bank receipt image or PDF is clear, and re-submit your payment slip directly via your workspace billing portal.
            </p>

            <div class="btn-container">
                <a href="{{ url('/app/' . $company->slug . '/billing') }}" class="btn">Go to Billing & Resubmit Slip &rarr;</a>
            </div>

            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                If you believe this is a misunderstanding, please reach out directly with your transfer proof to <a href="mailto:billing@pixelcraftlk.com" style="color: #e11d48;">billing@pixelcraftlk.com</a>.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PixelCraft LK. All rights reserved.<br>
            Secure Multi-Tenant Cloud ERP & Point of Sale System.
        </div>
    </div>
</body>
</html>
