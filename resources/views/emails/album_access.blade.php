<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Album Access</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}!</h1>

    <p>We are excited to let you know that your album is ready for viewing.</p>

    <p>
        <a href="{{ $albumUrl }}" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            View Album
        </a>
    </p>

    <p>Alternatively, you can copy the link below:</p>
    <p>{{ $albumUrl }}</p>

    <p>To copy the link, simply highlight it and use Ctrl+C (Windows) or Command+C (Mac).</p>

    <p>Thank you for using our service!</p>

    <p>Best Regards,<br>The Photographer Team</p>
</body>
</html>