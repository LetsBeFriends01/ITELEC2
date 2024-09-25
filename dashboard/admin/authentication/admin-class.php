<?php

require_once __DIR__.'/../../../database/dbconnection.php';
include_once __DIR__.'/../../../config/settings-configuration.php';
require_once __DIR__.'/../../../src/vendor/autoload.php';
require_once __DIR__.'/../../../lib/emailTemplate.php';
ini_set('memory_limit', '2024M');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ADMIN {
    private $conn;
    private $settings;
    private $smtp_email;
    private $smtp_password;


    public function __construct() {
        $this->settings = new SystemConfig();
        $this->smtp_email = $this->settings->getSmtpEmail();
        $this->smtp_password = $this->settings->getSmtpPassword();

        $database = new Database();
        $this->conn = $database->dbConnection();
    }
    
    public function sendOtp($email, $otp) {
        if($email == NULL) {
            echo "<script>alert('No email found'); window.location.href = '../../../';</script>";
            exit;
        } else {
            $stmt = $this->runQuery("SELECT * FROM user WHERE email = :email");
            $stmt->execute(array(":email" => $email));
            $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Email already taken!'); window.location.href = '../../../';</script>";
                exit;
            } else {
                $_SESSION['OTP'] = $otp;

                $subject = "OTP VERIFICATION";
                $template = new EmailTemplate();
                $this->template = $template->getTemplateToVerify($email, $otp);
                $message = $this->template;

                $this->send_email($email, $message, $subject, $this->smtp_email, $this->smtp_password);
                echo "<script>alert('OTP sent to $email'); window.location.href = '../../../verify-otp.php';</script>";
            }
        }
    }

    public function verifyOTP($username, $email, $password, $tokencode, $otp, $csrf_token) {
        if($otp == $_SESSION['OTP']) {
            unset($_SESSION['OTP']);

            $this->addAdmin($csrf_token, $username, $email, $password);
            $subject = "VERIFICATION SUCCESS";
            $template = new EmailTemplate();
            $message = $template->getTemplateForSuccess($email);
 
            $this->send_email($email, $message, $subject, $this->smtp_email, $this->smtp_password);

            unset($_SESSION['not_verify_username']);
            unset($_SESSION['not_verify_email']);
            unset($_SESSION['not_verify_password']);
        } else if ($otp == NULL) {
            echo "<script>alert('No OTP found'); window.location.href = '../../../verify-otp.php';</script>";
        } else {
            echo "<script>alert('Invalid OTP'); window.location.href = '../../../verify-otp.php';</script>";
        }
    }

    public function addAdmin($csrf_token, $username, $email, $password) {
        $stmt = $this->runQuery("SELECT * FROM user WHERE email = :email");
        $stmt->execute(array(":email" => $email));

        if($stmt->rowCount() > 0) {
            echo "<script>alert('Email already exists!'); window.location.href = '../../..'</script>";
            exit;
        }

          if(!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            echo "<script>alert('Invalid CSRF token!'); window.location.href = '../../..'</script>";
            exit;
        }

        // unset($_SESSION['csrf_token']);

        $hash_password = md5($password);
        echo `<script>alert('admin Added Successfully'); window.location.href = '../../../';</script>`;

        $stmt = $this->runQuery('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
        $exec = $stmt->execute(array(
            ":username"=> $username,
            ":email" => $email,
            ":password" => $hash_password
        ));

        if($exec) {
            echo "<script>alert('admin Added Successfully'); </script>";
            // echo "<script>alert('admin Added Successfully'); window.location.href = '../../../';</script>";
            $this->adminSignIn($email, $password, $csrf_token);
            exit;
        } else {
             echo "<script>alert('adding admin failed'); window.location.href = '../../../';</script>";
            exit; 
        }
    }

     public function adminSignIn($email, $password, $csrf_token) {
        try {
            if(!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            echo "<script>alert('Invalid CSRF token!'); window.location.href = '../../..'</script>";
            exit; 
        }

            unset($_SESSION['csrf_token']);

             $stmt = $this->runQuery("SELECT * FROM user WHERE email = :email");

            $stmt->execute(array(":email" => $email));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            // if($userRow->rowCount() == 1) {
            //     if($userRow['status'] == "active") {
            //         if($userRow['password'] == md5($password)) {
            //             $activity = "Has Successfully signed in";
            //             $user_id = $userRow['id'];
            //             $this->logs($activity, $user_id);

            //             $_SESSION['adminSession'] = $user_id;
            //             echo "<script>alert('Welcome To IT ELEC 2!'); window.location.href = '../'</script>";
            //         } else {
            //             echo "<script>alert('Incorrect Password'); window.location.href = '../../..'</script>";
            //             exit;
            //         }
            //     } else {
            //         echo "<script>alert('Your account is not verified!'); window.location.href = '../../..'</script>";
            //         exit;
            //     }
            // } else {
            //     echo "<script>alert('No account found'); window.location.href = '../../..'</script>";
            // }
             
            if($stmt->rowCount() == 1 && $userRow['password'] == md5($password)) {
                $activity = "Has Successfully signed in";
                $user_id = $userRow['id'];
                $this->logs($activity, $user_id);

                $_SESSION['adminSession'] = $user_id;
                 echo "<script>alert('Welcome To IT ELEC 2!'); window.location.href = '../'</script>";
                exit;
            } else {
                echo "<script>alert('Invalid Credentials'); window.location.href = '../../..'</script>";
                exit;
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    } 

     public function adminSignOut() {
        unset($_SESSION['adminSession']);
        echo "<script>alert('Signed Out!'); window.location.href = '../../../'</script>";
    }

     public function logs($activity, $user_id ) {
        $stmt = $this->runQuery("INSERT INTO logs (user_id, activity) VALUES (:user_id, :activity)");
        $stmt->execute(array(":user_id" => $user_id, ":activity" => $activity));
    }

    function send_email($email, $message, $subject, $smtp_email, $smtp_password) {

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->isHTML();
        $mail->addAddress($email);
        $mail->Username = $smtp_email;
        $mail->Password = $smtp_password;
        $mail->setFrom($smtp_email, "Angelo");
        $mail->Subject = $subject;
        // $mail->Body = $message;
        $mail->msgHTML($message);
        $mail->send();
        
    }

    public function isUserLoggedIn() {
        if(isset($_SESSION['adminSession'])){
            return true;
            exit;
        } else {
            $this->redir();
            return false;
        }
    }  

    public function redir() {
        echo "<script>alert('admin must login first'); window.location.href = '../../';</script>";
        exit;
    }

    public function signOut() {
        echo "<script>alert('admin must login first'); window.location.href = '../../';</script>";
        exit;
    }
 
     public function runQuery($sql) {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
}

if (isset($_POST['btn-signup'])) {

    $_SESSION['not_verify_csrf_token'] = trim($_POST['csrf_token']);
    $_SESSION['not_verify_username'] = trim($_POST['username']);
    $_SESSION['not_verify_email'] = trim($_POST['email']);
    $_SESSION['not_verify_password'] = trim($_POST['password']);
    // $csrf_token = trim($_POST['csrf_token']);
    // $username = trim($_POST['username']);
    // $email = trim($_POST['email']);
    // $password = trim($_POST['password']);

    $email = trim($_POST['email']);
    $otp = rand(100000, 999999);
    $addAdmin = new ADMIN();
    $addAdmin->sendOtp($email, $otp);
}

if (isset($_POST['btn-verify'])) {
    $csrf_token = trim($_POST['csrf_token']);
    $username = $_SESSION['not_verify_username'];
    $email = $_SESSION['not_verify_email'];
    $password = $_SESSION['not_verify_password'];

    $tokencode = md5(uniqid(rand()));
    $otp = trim($_POST['otp']);

    $adminSignIn = new ADMIN;
    $adminSignIn->verifyOTP($username, $email, $password, $tokencode, $otp, $csrf_token);
}


if (isset($_POST['btn-signin'])) {
    $csrf_token = trim($_POST['csrf_token']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $adminSignIn = new ADMIN;
    $adminSignIn->adminSignIn($email, $password, $csrf_token);
}

if (isset($_GET['admin_signout'])) {
    $adminSignOut = new ADMIN();
    $adminSignOut->adminSignOut();
}

?>