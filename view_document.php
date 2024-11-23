<?php
@include 'config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit;
}

// Get the tracking ID from the URL
if (isset($_GET['tracking_id']) && !empty($_GET['tracking_id'])) {
    $tracking_id = mysqli_real_escape_string($conn, $_GET['tracking_id']);

    // Query to allow users to view only their own document or admin to view any
    if ($_SESSION['user_type'] === 'admin') {
        $query = "SELECT status, file_path, document_name FROM documents WHERE tracking_id = '$tracking_id'";
    } else {
        $query = "SELECT status, file_path, document_name FROM documents WHERE tracking_id = '$tracking_id' AND user_id = '{$_SESSION['user_id']}'";
    }

    // Execute the query
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $document = mysqli_fetch_assoc($result);
        
        // Proceed to display the document only if the file exists
        $filePath = $document['file_path'];
        $fileName = $document['document_name'];

        // Check if the file exists on the server
        if (!file_exists($filePath)) {
            echo "File not found on server: " . $filePath;
            exit;
        }

        // Determine the file extension and set the Content-Type accordingly
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        switch(strtolower($fileExtension)) {
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'txt':
                header('Content-Type: text/plain');
                break;
            case 'html':
                header('Content-Type: text/html');
                break;
            case 'docx':
            case 'doc':
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                break;
            default:
                header('Content-Type: application/octet-stream');
                break;
        }

        // Set headers for inline viewing (not download)
        header("Content-Disposition: inline; filename=\"$fileName\"");

        // Read and display the file content
        readfile($filePath);
        exit;
    } else {
        echo "Document not found or you do not have permission to view it.";
    }
} else {
    echo "Invalid request or missing tracking ID.";
}
?>
