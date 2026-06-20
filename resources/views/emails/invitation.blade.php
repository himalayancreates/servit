<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're invited to ServIt</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f9fafb; color: #111827; }
        .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #111827; padding: 28px 36px; }
        .logo { font-size: 22px; font-weight: 800; color: #f97316; letter-spacing: -0.5px; }
        .body { padding: 36px; }
        .headline { font-size: 22px; font-weight: 700; color: #111827; line-height: 1.3; margin-bottom: 12px; }
        .text { font-size: 15px; color: #6b7280; line-height: 1.6; margin-bottom: 16px; }
        .highlight { color: #111827; font-weight: 600; }
        .cta-wrap { text-align: center; margin: 32px 0; }
        .cta { display: inline-block; background: #f97316; color: #ffffff; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 32px; border-radius: 8px; }
        .divider { border: none; border-top: 1px solid #f3f4f6; margin: 28px 0; }
        .url-fallback { font-size: 12px; color: #9ca3af; word-break: break-all; }
        .url-fallback a { color: #f97316; }
        .expiry { font-size: 13px; color: #9ca3af; margin-top: 12px; }
        .footer { padding: 20px 36px; background: #f9fafb; border-top: 1px solid #f3f4f6; }
        .footer-text { font-size: 12px; color: #9ca3af; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="logo">ServIt</div>
            </div>

            <div class="body">
                <p class="headline">You're invited to set up your restaurant on ServIt</p>

                <p class="text">
                    Someone from the ServIt team has invited you to create your online ordering page.
                    It takes less than 2 minutes to get started — no credit card required.
                </p>

                <p class="text">
                    Get online ordering, menu management, reservation booking, and more — all in one place.
                </p>

                <div class="cta-wrap">
                    <a href="{{ $inviteUrl }}" class="cta">Set up your restaurant →</a>
                </div>

                <hr class="divider">

                <p class="url-fallback">
                    If the button doesn't work, paste this link into your browser:<br>
                    <a href="{{ $inviteUrl }}">{{ $inviteUrl }}</a>
                </p>

                <p class="expiry">This invite link expires in 7 days.</p>
            </div>

            <div class="footer">
                <p class="footer-text">
                    This email was sent to <strong>{{ $invitation->email }}</strong> because someone at ServIt
                    sent you an invitation. If this was a mistake, you can ignore it.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
