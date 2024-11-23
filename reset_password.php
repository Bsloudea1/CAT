<?php
@include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Get the token data from the database
    $select_token = "SELECT * FROM password_resets WHERE token = '$token'";
    $result_token = mysqli_query($conn, $select_token);

    if (mysqli_num_rows($result_token) > 0) {
        $row = mysqli_fetch_array($result_token);
        $expiration_time = strtotime($row['expiration_time']); // Convert expiration time to a timestamp
        $current_time = time(); // Current timestamp

        // Check if the token is expired
        if ($current_time > $expiration_time) {
            $message = "The password reset link has expired.";
        } else {
            // Token is valid, proceed with the password reset
            if (isset($_POST['submit'])) {
                $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
                $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

                if ($new_password == $confirm_password) {
                    // Hash the new password
                    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

                    // Get user ID associated with the token
                    $user_id = $row['user_id'];

                    // Update the user's password in the database
                    $update_password = "UPDATE user_form SET password = '$new_password_hashed' WHERE id = '$user_id'";
                    if (mysqli_query($conn, $update_password)) {
                        $message = "Your password has been successfully reset.";
                        // Redirect to login after a successful reset (using JavaScript for toast)
                        echo "<script>window.location.href = 'login_form.php';</script>";
                    } else {
                        $message = "Failed to reset the password. Please try again.";
                    }
                } else {
                    $message = "Passwords do not match.";
                }
            }
        }
    } else {
        $message = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            left: 47%;
            top: 30px;
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
        <form action="reset_password.php?token=<?php echo $_GET['token']; ?>" method="post">
            <h3>Reset Password</h3>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <input type="submit" name="submit" value="Reset Password" class="form-btn">
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
