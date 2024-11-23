<?php
session_start();  // Ensure session is started at the top

@include 'config.php';

// Debugging session variable to ensure it's set
if (!isset($_SESSION['user_type'])) {
    echo 'Session not set!';
    exit;  // Stop the script execution if session is not set
}

if ($_SESSION['user_type'] !== 'admin') {
    header('Location: login_form.php');
    exit;
}

if (isset($_GET['id'], $_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    // Debugging the values of 'id' and 'status'
    echo 'ID: ' . $id . ' Status: ' . $status;

    if (in_array($status, ['Accepted', 'Declined'])) {
        $query = "UPDATE documents SET status = '$status', last_updated = NOW() WHERE id = $id";
        
        // Debugging the query before execution
        echo 'Query: ' . $query;

        if (mysqli_query($conn, $query)) {
            header("Location: admin_documents.php?status=Pending");
            exit;
        } else {
            echo "Failed to update document status. Error: " . mysqli_error($conn);
        }
    }
} else {
    echo "Invalid parameters.";
}
?>
