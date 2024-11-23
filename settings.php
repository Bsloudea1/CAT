<?php
@include 'config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login_form.php');
    exit();
}

// Fetch user profile data
$user_id = $_SESSION['user_id'];
$select_profile = "SELECT * FROM user_profile WHERE user_id = '$user_id'";
$profile_result = mysqli_query($conn, $select_profile);

// Initialize profile data if no result is found
if (mysqli_num_rows($profile_result) > 0) {
    $profile_data = mysqli_fetch_assoc($profile_result);
} else {
    $profile_data = [
        'name' => '',
        'email' => '',
        'user_profile' => '' // Default to no profile picture   
    ];
}

$home_link = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin' 
    ? 'admin_page.php' 
    : 'user_page.php';

$document_link = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin' 
    ? 'admin_documents.php' 
    : 'user_documents.php';

// Handle form submission to update profile
if (isset($_POST['save_changes'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Update profile picture if uploaded
    $profile_pic = $profile_data['user_profile']; // Keep the current picture if none uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file = $_FILES['profile_picture'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file type
        if (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
            $new_file_name = "profile_" . $user_id . "." . $file_ext;
            move_uploaded_file($file_tmp, "uploads/" . $new_file_name); // Store the file
            $profile_pic = "uploads/" . $new_file_name; // Update the profile picture path
        }
    }

    // Update profile in the database
    $update_profile = "UPDATE user_profile SET name = '$name', email = '$email', user_profile = '$profile_pic' WHERE user_id = '$user_id'";
    mysqli_query($conn, $update_profile);

    if (isset($_POST['save_changes'])) {
        // Update profile data...
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_profile'] = $profile_pic;
    
        // Redirect to refresh page and retain session data
        header('location:settings.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="THESIS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
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
</style>
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
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <div class="profile-photo">
                    <?php if (!empty($profile_data['user_profile'])): ?>
                        <img src="<?php echo htmlspecialchars($profile_data['user_profile']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <p>No profile picture uploaded yet.</p>
                    <?php endif; ?>
                    <input type="file" name="profile_picture" accept="image/jpeg, image/png">
                </div>
            </div>

            <div class="form-group2">
                <label for="name" style="margin-top: 5px;">Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($profile_data['name']); ?>" required>
            </div>          
            <div class="form-group2">
                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($profile_data['email']); ?>" required>
            </div>
            <button type="submit" name="save_changes">Save Changes</button>
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

</body>
    <script>
        let btn = document.querySelector('#btn');
        let sidebar = document.querySelector('.sidebar');

        btn.onclick = function () {
            sidebar.classList.toggle('active');
        };
    </script>
</html>
