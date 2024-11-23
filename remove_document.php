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

    // Modify the query to allow admins to view all documents
    if ($_SESSION['user_type'] === 'admin') {
        // Admin can view all documents, no user_id filter
        $query = "SELECT status, file_path, document_name FROM documents WHERE tracking_id = '$tracking_id'";
    } else {
        // Regular users can only view their own documents
        $query = "SELECT status, file_path, document_name FROM documents WHERE tracking_id = '$tracking_id' AND user_id = '{$_SESSION['user_id']}'";
    }

    // Execute the query
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $document = mysqli_fetch_assoc($result);
        
        // Check if the document's status is not 'Pending' before allowing removal
        if ($document['status'] !== 'Pending') {
            echo "You can only remove documents that are still pending.";
            exit; // Prevent further execution
        }

        // Proceed with removal if the status is 'Pending'
        $deleteQuery = "DELETE FROM documents WHERE tracking_id = '$tracking_id' AND user_id = '{$_SESSION['user_id']}'";
        if (mysqli_query($conn, $deleteQuery)) {
            // Optional: Delete the file from the server too
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file from the server
            }

            echo "Document removed successfully.";
        } else {
            echo "Error removing document.";
        }
    } else {
        echo "Document not found or you do not have permission to remove it.";
    }
} else {
    echo "Invalid request or missing tracking ID.";
}
?>
