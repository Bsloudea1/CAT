<?php
session_start();
include 'config.php'; // Database connection file

// Define a message variable for the toast
$message = "";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$home_link = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin' 
    ? 'admin_page.php' 
    : 'user_page.php';

$document_link = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin' 
    ? 'admin_documents.php' 
    : 'user_documents.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if new password matches the confirmation
    if ($newPassword !== $confirmPassword) {
        $message = "New passwords do not match!";
    } else {
        // Fetch the current password hash from the database
        $query = "SELECT password FROM user_form WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();

        // Verify the current password
        if (!password_verify($currentPassword, $hashedPassword)) {
            $message = "Current password is incorrect!";
        } else {
            // Hash the new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update Pass in db
            $updateQuery = "UPDATE user_form SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $newHashedPassword, $userId);

            if ($updateStmt->execute()) {
                $message = "Password updated successfully!";
            } else {
                $message = "Failed to update password. Please try again.";
            }

            $updateStmt->close();
        }
    }
    $conn->close();
}
?>

<!-- Toast Notification -->
<div id="toast" class="toast">
    <p id="toastMessage"><?php echo $message; ?></p>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="THESIS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body {
        min-height: 100vh;
        width: auto;
        background-image: url(bg/C.A.T\ bg3.jpg);
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        overflow-x: hidden;
        overflow-y: hidden;
    }
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
        left: 50%;
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

    /* RESPONSIVE SMALL SCREEN */

    @media (max-width: 500px) {
    form {
        padding: 15px;
        margin: 20px;
    }
    h1{
        margin-top: -25px;
    }
    input[type="password"] {
        padding: 8px;
        font-size: 12px;
    }

    button[type="submit"] {
        padding: 8px;
        font-size: 14px;
    }
}
</style>
</head>
<body>
<div class="main-settings-container">
        <div class="settings-container">
        <div class="top-navbar">
            <ul>
                <li><a href="<?php echo $home_link; ?>"><i class="fa-solid fa-house-chimney"></i> Home</a></li>
                <li><a href="settings.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                <li><a href="chngepassword.php"><i class="fa-solid fa-lock"></i> Password</a></li>
            </ul>
        </div>
            <form action="chngepassword.php" method="POST">
                <h1 style="margin-bottom: 10px;">PASSWORD SETTINGS</h1>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
                
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
                
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                
                <button type="submit">Save Password</button>
            </form>

        </div>
    </div>

    <div class="sidebar">
        <div class="top">
            <div class="logo">
                    <img src="#" alt="C.A.T LOGO" class="catlogo">
                <span>MENU</span>
            </div>
            <i class="fa-solid fa-bars" id="btn"></i>
        </div>
        <div class="profile">
            <img src="<?php echo isset($_SESSION['user_profile']) ? $_SESSION['user_profile'] : 'default_profile_pic.png'; ?>" 
                alt="Profile Picture">
            <p><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User Name'; ?></p>
        </div> 
        <ul>
            <li>
                <a href="<?php echo $home_link; ?>">
                    <i class="fa-solid fa-house-chimney"></i>
                    <span class="nav-item">Home</span>
                </a>
                <span class="tooltip">Home</span>
            </li>
            <li>
                <a href="<?php echo $document_link; ?>">
                    <i class="fa-solid fa-folder-open"></i>
                    <span class="nav-item">Document</span>
                </a>
                <span class="tooltip">Document</span>
            </li>
            <li>
                <a href="settings.php">
                    <i class="fa-solid fa-gear"></i>
                    <span class="nav-item">Setting</span>
                </a>
                <span class="tooltip">Setting</span>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Logout</span>
                </a>
                <span class="tooltip">Logout</span>
            </li>
        </ul>
    </div>
    
    <div id="toast" class="toast">
        <p id="toastMessage"><?php echo $message; ?></p>
    </div>

    <script>
            let btn = document.querySelector('#btn');
            let sidebar = document.querySelector('.sidebar');

            btn.onclick = function () {
                sidebar.classList.toggle('active');
            };

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