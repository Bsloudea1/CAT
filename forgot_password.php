<?php
@include 'config.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

$message = '';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the database
    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $user_id = $row['id'];

        // Generate a unique reset token (valid for 5 minutes)
        $reset_token = bin2hex(random_bytes(50)); // Generate a secure token
        $expiration_time = date('Y-m-d H:i:s', strtotime('+5 minutes')); // 5 minutes from now

        // Store the token and expiration time in the database
        $insert_token = "INSERT INTO password_resets (user_id, token, expiration_time) VALUES ('$user_id', '$reset_token', '$expiration_time')";
        mysqli_query($conn, $insert_token);

        // Send a reset email using PHPMailer
        $reset_link = "http://localhost/CAT/reset_password.php?token=$reset_token";

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'demonluke24@gmail.com';                // SMTP username
            $mail->Password   = 'VPOC FXFN BMAN OPMF';                  // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
            $mail->Port       = 465;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom('demonluke24@gmail.com', 'C.A.T Admin');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link below to reset your password:<br><br><a href='$reset_link'>$reset_link</a>";

            $mail->send();
            $message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "This email address is not registered.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="THESIS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 47%; /* Center horizontally */
            top: 30px; /* Vertical position */
            font-size: 17px;
        }
        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @keyframes fadein {
            from {top: 0; opacity: 0;}
            to {top: 30px; opacity: 1;}
        }

        @keyframes fadeout {
            from {top: 30px; opacity: 1;}
            to {top: 0; opacity: 0;}
        }
        @media screen and (max-width: 768px) {
        .toast {
            font-size: 15px; 
            justify-content: center;
            align-items: center;
            display: flex;
            padding: 1rem;
        }
    }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="#" method="post">
            <h3>Forgot Password</h3>
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="submit" name="submit" value="Reset Password" class="form-btn">
            <p>Remembered your password? <a href="login_form.php">Login</a></p>
        </form>
    </div>

    <div id="toast" class="toast">
        <p id="toastMessage"><?php echo $message; ?></p>
    </div>

    <script>
            function showToast(message) {
                var toast = document.getElementById('toast');
                document.getElementById('toastMessage').innerText = message;
                toast.className = "toast show";
                setTimeout(function() {
                    toast.className = toast.className.replace("show", "");
                }, 3000);
            }

            <?php if (!empty($message)): ?>
                showToast("<?php echo $message; ?>");
            <?php endif; ?>
    </script>
</body>
</html>
