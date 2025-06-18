<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body, h1, p {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #222222;
        }
        p {
            font-size: 16px;
            margin-bottom: 25px;
        }
        .verification-code {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 6px;
            font-size: 20px;
            letter-spacing: 2px;
            text-align: center;
        }
        .footer {
            font-size: 14px;
            color: #888888;
            margin-top: 30px;
            text-align: center;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        <p>Thank you for signing up! To verify your email address, please use the code below:</p>
        <div class="verification-code">{{$code}}</div>
        <p class="footer">
            This code will expire in {{$expiresAfter}} hours.<br>
            If you did not request this, you can safely ignore this email.<br><br>
            &mdash; The {{ config('app.name') }} Team
        </p>
    </div>
</body>
</html>
