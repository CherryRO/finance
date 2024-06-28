<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .email-wrapper {
            background-color: #f4f4f4;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .logo {
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 10px;
        }
        p {
            color: #666666;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            color: #ffffff;
            background-color: #536DFE;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #3d52c7;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="39" height="22" viewBox="0 0 39 22" fill="none">
                    <rect width="7.37565" height="21.1131" rx="3.68783" transform="matrix(-0.865206 0.501417 0.498585 0.866841 27.425 0)" fill="currentColor"></rect>
                    <rect width="7.37565" height="21.1131" rx="3.68783" transform="matrix(-0.865206 0.501417 0.498585 0.866841 27.5004 0)" fill="url(#paint0_linear_1041_5978)" fill-opacity="0.4"></rect>
                    <rect width="7.37565" height="21.1131" rx="3.68783" transform="matrix(0.865206 0.501417 -0.498585 0.866841 24.6698 0)" fill="currentColor"></rect>
                    <rect width="7.37565" height="21.1131" rx="3.68783" transform="matrix(-0.865206 0.501417 0.498585 0.866841 13.3428 0)" fill="currentColor"></rect>
                    <rect width="7.37565" height="21.1131" rx="3.68783" transform="matrix(-0.865206 0.501417 0.498585 0.866841 13.3815 0)" fill="url(#paint1_linear_1041_5978)" fill-opacity="0.4"></rect>
                    <rect width="7.37565" height="21.1131" rx="3.68783" transform="matrix(0.865206 0.501417 -0.498585 0.866841 10.5267 0)" fill="currentColor"></rect>
                    <defs>
                        <linearGradient id="paint0_linear_1041_5978" x1="3.68783" y1="0" x2="3.68783" y2="21.1131" gradientUnits="userSpaceOnUse">
                            <stop></stop>
                            <stop offset="1" stop-opacity="0"></stop>
                        </linearGradient>
                        <linearGradient id="paint1_linear_1041_5978" x1="3.68783" y1="0" x2="3.68783" y2="21.1131" gradientUnits="userSpaceOnUse">
                            <stop></stop>
                            <stop offset="1" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <h1>Reset your password</h1>
            <p>Click on the button below to reset your password</p>
            <a href="{{ url('reset-password?token='.$token.'&email='.$email) }}" class="button">Reset Password</a>
        </div>
    </div>
</body>
</html>
