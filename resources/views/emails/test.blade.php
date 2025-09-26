<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $notification }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="background-color: #4CAF50; color: white; padding: 20px; text-align: center;">
            <h1>{{ $notification }}</h1>
        </div>
        <div style="padding: 20px;">
            <p>Hi {{ $user_name }},</p>
            <p>{!! $messages !!}</p>
        </div>
    </div>
</body>
</html>
