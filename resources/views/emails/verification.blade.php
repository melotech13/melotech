<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - MeloTech</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c5530;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            color: #2c5530;
            margin-bottom: 20px;
        }
        .verification-code {
            background-color: #f8f9fa;
            border: 2px solid #2c5530;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #2c5530;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .instructions {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #2c5530;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🌱 MeloTech</div>
            <h1 class="title">Verify Your Email Address</h1>
        </div>

        <p>Hello {{ $user->name }},</p>

        <p>Thank you for registering with MeloTech! To complete your registration and access your dashboard, please verify your email address using the verification code below:</p>

        <div class="verification-code">
            <p style="margin: 0 0 10px 0; font-weight: bold;">Your verification code is:</p>
            <div class="code">{{ $verificationCode }}</div>
        </div>

        <div class="instructions">
            <h3 style="margin-top: 0; color: #2c5530;">How to verify:</h3>
            <ol>
                <li>Copy the verification code above</li>
                <li>Go back to the verification page in your browser</li>
                <li>Enter the code in the verification field</li>
                <li>Click "Verify Email" to complete your registration</li>
            </ol>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong> This verification code will expire in 15 minutes for security reasons. If you don't verify your email within this time, you'll need to request a new verification code.
        </div>

        <p>If you didn't create an account with MeloTech, please ignore this email.</p>

        <div class="footer">
            <p>This email was sent from MeloTech - Your Smart Farming Companion</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>




