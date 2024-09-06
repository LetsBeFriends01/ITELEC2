<?php

require_once 'authentication/admin-class.php';

$admin = new ADMIN();
$admin->isUserLoggedIn();

$stmt = $admin->runQuery("SELECT * FROM user WHERE id = :id");
$stmt->execute(array(":id" => $_SESSION['adminSession']));
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome <?php echo $user_data['username'] ?></h1>
    <button><a href="authentication/admin-class.php?admin_signout" >Sign Out</a></button>
</body>
</html>  