<?php

class EmailTemplate {

    // OTP Verification Template
    public function getTemplateToVerify($email, $otp) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification</title>
            <style>
                body {
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                }
                h1 {
                    font-size: 24px;
                    color: #333;
                    margin: 0;
                }
                p {
                    font-size: 16px;
                    color: #555;
                }
                .otp {
                    font-size: 22px;
                    font-weight: bold;
                    color: #000;
                    letter-spacing: 4px;
                    text-align: center;
                    margin: 20px 0;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #888;
                    padding-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>OTP Verification</h1>
                </div>
                <p>Hi <strong>$email</strong>,</p>
                <p>Your OTP code is:</p>
                <div class='otp'>$otp</div>
                <p>Please use this code to complete your verification. The code is valid for the next 10 minutes.</p>
                <div class='footer'>
                    <p>If you didn't request this, please ignore this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    // OTP Verification Success Template
    public function getTemplateForSuccess($email) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification Success</title>
            <style>
                body {
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                }
                h1 {
                    font-size: 24px;
                    color: #333;
                    margin: 0;
                }
                p {
                    font-size: 16px;
                    color: #555;
                    line-height: 1.5;
                }
                .success {
                    font-size: 18px;
                    font-weight: bold;
                    color: #28a745;
                    text-align: center;
                    margin: 20px 0;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #888;
                    padding-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>OTP Verification Successful</h1>
                </div>
                <p>Hello <strong>$email</strong>,</p>
                <p>Your OTP verification has been successfully completed.</p>
                <div class='success'>Congratulations! Your account is now verified.</div>
                <p>You can now proceed to access all our features.</p>
                <p>If you did not complete this verification, please contact our support team immediately.</p>
                <div class='footer'>
                    <p>Thank you for using our service!</p>
                    <p>If you have any questions, feel free to <a href='mailto:support@yourdomain.com'>contact us</a>.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
