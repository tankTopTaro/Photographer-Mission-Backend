<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Invitation</title>
</head>
<body>
    <h1>Hello,</h1>

    <p>You have been invited to view an album!</p>

    <p>
        <a href="{{ $albumUrl }}" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            View Album
        </a>
    </p>

    <p>To access the album, simply click the button above.</p>

    <p>Alternatively, you can copy the link below:</p>
    <p>{{ $albumUrl }}</p>

    <p>To copy the link, simply highlight it and use Ctrl+C (Windows) or Command+C (Mac).</p>

    <p>Thank you for being a part of our community!</p>

    <p>Best Regards,<br>The Photographer Team</p>
</body>
</html>
