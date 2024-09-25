<?php
    include_once 'config/settings-configuration.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="src/css/main.css">
    <link rel="stylesheet" href="src/css/otp.css">
        <link rel="shortcut icon" href="src/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="center">
        <div class="otp-container">
            <h1>Enter OTP</h1>
            <form action="dashboard/admin/authentication/admin-class.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?>">
                <input type="number" name="otp" placeholder="Enter OTP" required>
                <button type="submit" name="btn-verify">Verify</button>
            </form>
            <p class="info">Please enter the OTP sent to your email.</p>
        </div>
    </div>
</body>
</html>
