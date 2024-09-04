<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Hi {{ $user->username }},</h1>
    <p>Welcome to our application! Please verify your email address by clicking the button below:</p>
    <a href="{{ $url }}" class="button">Verify Email Address</a>
    <p>If you did not create an account, no further action is required.</p>
    <p>Best regards,<br>The Team</p>
</body>
</html>
