<?php
include 'config.php'; // No error suppression here!

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT name FROM user_form WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
if (!$user_result) {
    // Handle error without displaying sensitive info
    header('Location: error_page.php');
    exit;
}
$user_row = mysqli_fetch_assoc($user_result);
$recipient_name = $user_row['name']; // Use logged-in user's name as recipient_name

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $status = 'Pending'; // Set status to Pending by default
    $file = $_FILES['document'];
    $document_type = ''; // Initialize document type

    // Check if there was an error with the file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Location: error_page.php'); // Redirect to a generic error page
        exit();
    }

    $fileName = basename($file['name']);
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Check if the file type is PNG/JPG
    if (in_array(strtolower($fileExtension), ['png', 'jpg', 'jpeg'])) {
        header('Location: error_page.php?error=invalid_file_type'); // Redirect with error parameter
        exit();
    }

    // Allowed file types
    $allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];

    // Check if the file extension is in the allowed list
    if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
        header('Location: error_page.php?error=invalid_file_type'); // Redirect with error parameter
        exit();
    }

    // Set document type based on file extension
    $document_type = ucfirst(strtolower($fileExtension)) . " Document";

    // Upload the document
    $uploadDir = 'uploads/';
    $newFileName = uniqid() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $newFileName;

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
        // Generate tracking ID
        $tracking_id = uniqid('#'); // Generate a unique tracking ID

        // Prepare SQL query
        $query = "INSERT INTO documents (user_id, tracking_id, recipient_name, document_type, document_name, file_path, status, submission_date)
                  VALUES ('$user_id', '$tracking_id', '$recipient_name', '$document_type', '$fileName', '$uploadPath', '$status', NOW())";
        
        // Execute the query and check for errors
        $result = mysqli_query($conn, $query);
        if ($result) {
            $_SESSION['success_message'] = "Document submitted successfully!";
            header("Location: user_documents.php");
            exit(); // Ensure the script stops here after success
        } else {
            header('Location: error_page.php'); // Redirect to error page on SQL failure
            exit();
        }
    } else {
        header('Location: error_page.php'); // Redirect to error page if upload fails
        exit();
    }
}
?>
