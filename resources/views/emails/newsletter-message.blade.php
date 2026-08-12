<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mailSubject ?? 'Newsletter' }}</title>
</head>
<body style="margin: 0; background: #f7f3ef; color: #211915; font-family: Arial, sans-serif;">
    <div style="max-width: 680px; margin: 0 auto; padding: 32px 20px;">
        <div style="border: 1px solid #eadfd5; border-radius: 12px; background: #ffffff; padding: 28px;">
            {!! $mailBody !!}
        </div>
    </div>
</body>
</html>
