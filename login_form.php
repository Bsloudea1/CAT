<?php
@include 'config.php';

session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // Select the user based on email
    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // Fetch the stored hashed password from the database
        $storedHashedPassword = $row['password'];

        // Verify the entered password against the stored hash
        if (password_verify($pass, $storedHashedPassword)) {
            // Fetch user profile information
            $user_id = $row['id']; // Assuming 'id' is the user's unique ID
            $select_profile = "SELECT * FROM user_profile WHERE user_id = '$user_id'";
            $profile_result = mysqli_query($conn, $select_profile);
            
            if (mysqli_num_rows($profile_result) > 0) {
                // If profile exists, fetch the data
                $profile_data = mysqli_fetch_assoc($profile_result);
            } else {
                // If no profile exists, insert default data
                $profile_data = [
                    'user_profile' => '', // No profile picture if not found
                    'name' => $row['name'], // Set name from user_form table
                    'email' => $row['email'] // Set email from user_form table
                ];

                // Insert profile data into user_profile table
                $insert_profile = "INSERT INTO user_profile (user_id, name, email, user_profile) VALUES ('$user_id', '{$profile_data['name']}', '{$profile_data['email']}', '{$profile_data['user_profile']}')";
                mysqli_query($conn, $insert_profile);
            }

            // Store user and profile data in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $profile_data['name'];
            $_SESSION['user_email'] = $profile_data['email'];
            $_SESSION['user_profile'] = $profile_data['user_profile']; // Store the profile picture path
            $_SESSION['user_type'] = $row['usertype'];

            // Redirect based on user type
            if ($row['usertype'] == 'admin') {
                header('location:admin_page.php');
            } elseif ($row['usertype'] == 'user') {
                header('location:user_page.php');
            }

        } else {
            $error[] = 'Incorrect email or password!';
        }
    } else {
        $error[] = 'Incorrect email or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login form</title>
    <link rel="stylesheet" href="THESIS.css">
    <link rel="icon" href="img/C.A.T LOGO.png" type="logo">
</head>
<body>
    <div class="form-container">

        <form action="#" method="post">
            <h3>Sign in</h3>
            <?php
            if(isset($error)){
                foreach($error as $error){
                    echo '<span class="error-msg">'.$error.'</span>';
                };
            };
            ?>
            <input type="email" name="email" required placeholder="enter your email">
            <input type="password" name="password" required placeholder="enter your password">
            <p style="float: left; padding: 5px; margin-top: 0;"><a href="forgot_password.php">Forgot Password?</a></p>
            <input type="submit" name="submit" value="login now" class="form-btn">
            <p>don't have an account? <a href="register_form.php">Sign up now</a></p>
        </form>
    </div>
</body>
</html>