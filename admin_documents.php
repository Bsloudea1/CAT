<?php

    @include 'config.php';

    session_start();

    // Check if the user is logged in and is an admin
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: login_form.php');
        exit;
    }

    $home_link = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin' 
        ? 'admin_page.php' 
        : 'user_page.php';

    $user_id = $_SESSION['user_id'];
    $status = isset($_GET['status']) ? $_GET['status'] : 'Pending';

    $query = "SELECT * FROM documents WHERE status = '$status' ORDER BY submission_date DESC";
    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Documents</title>
    <link rel="stylesheet" href="THESIS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    h1 {
        text-align: center;
        font-size: 2.5em;
        color: #333;
        margin-top: 30px;
    }
    #documentsTable {
        width: 100%;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    #documentsTable th, #documentsTable td {
        padding: 12px 90px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    #documentsTable th {
        background-color: #4CAF50;
        color: white;
        font-size: 1.1em;
    }
    #documentsTable td a {
        color: #4CAF50;
        text-decoration: none;
    }
    #documentsTable td a:hover {
        text-decoration: underline;
    }
    #documentsTable tr:hover {
        background-color: #f4f4f4;
    }
    .tabs {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    .tab {
        padding: 10px 20px;
        margin: 0 5px;
        background-color: #f4f4f4;
        border-radius: 5px;
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .tab.active {
        background-color: #4CAF50;
        color: white;
    }
    .tab:hover {
        background-color: #ddd;
    }
    #searchBar {
        width: 30%;
        padding: 8px;
        margin-left: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1em;
    }
    .btn-submit {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1.1em;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }
    .btn-submit:hover {
        background-color: #45a049;
    }
    .btn-download {
        padding: 8px 16px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-size: 1em;
        margin-left: 15px;
        border-style: none;
    }
    .btn-download:hover {
        background-color: #45a049;
    }
    .success-message {
        color: green;
        text-align: center;
        margin: 10px 0;
    }
    .error-message {
        color: red;
        text-align: center;
        margin: 10px 0;
    }
    .toast {
        visibility: hidden;
        max-width: 50px;
        height: 50px;
        margin-left: -125px;
        margin-top: -50px;
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
        animation: fadeInOut 3s ease-in-out;
    }
    @keyframes fadeInOut {
        0% { opacity: 0; }
        25% { opacity: 1; }
        75% { opacity: 1; }
        100% { opacity: 0; }
    }
@media only screen and (max-width: 768px) {
    .document_container {
        width: 100%;
        height: auto;
        padding: 3px;
        box-sizing: border-box;
        overflow-y: auto;
    }
    #documentsTable {
        width: 100%;  
        margin: 3px 0;
        max-height: auto;
        overflow-y: auto;
        display: block;
    }
    #documentsTable th, #documentsTable td {
        padding: 0.1px 82px ;
        text-align: left;
        border-bottom: 1px solid #ddd;
        font-size: 0.55em;
    }
    .tabs {
        flex-direction: column;
        align-items: flex-start;
        margin-top: 5px;
        margin-left: 80px;
    }
    .tab {
        padding: 3px 6px;
        font-size: 0.55em;
        margin: 2px 0;
        width: 100%;
    }
    #searchBar {
        font-size: 0.55em;
        width: 100%;
        margin-left: 0;
        align-items: center;
        justify-content: center;
        margin-top: 3px;
    }
    .btn-download {
        font-size: 0.55em;
        padding: 2px 4px;
        margin-left: 0;
        width: 100%;
        margin-top: 10px;
    }
}
</style>
<body>

<div class="sidebar" style="max-height: 100vh;">
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
                <a href="admin_documents.php">
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

    <h1>DOCUMENT</h1>
    <div class="tabs">
        <a href="?status=Pending" class="tab <?= $status == 'Pending' ? 'active' : '' ?>">Pending</a>
        <a href="?status=Accepted" class="tab <?= $status == 'Accepted' ? 'active' : '' ?>">Accepted</a>
        <a href="?status=Declined" class="tab <?= $status == 'Declined' ? 'active' : '' ?>">Declined</a>
        <input type="text" placeholder="Search..." id="searchBar" onkeyup="filterTable()">
    </div>

    <table id="documentsTable">
        <thead>
            <tr>
                <th>Tracking ID#</th>
                <th>Name of Recipient</th>
                <th>Document Type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['tracking_id'] ?></td>
                    <td><?= $row['recipient_name'] ?></td>
                    <td><?= $row['document_type'] ?></td>
                    <td><?= date('Y-m-d', strtotime($row['submission_date'])) ?></td>
                    <td><?= date('H:i', strtotime($row['submission_date'])) ?></td>
                    <td>
                    <a href="view_document.php?tracking_id=<?php echo urlencode($row['tracking_id']); ?>">View</a> |
                    <?php if ($status == 'Pending') { ?>
                        <a href="update_document_status.php?id=<?= $row['id'] ?>&status=Accepted">Accept</a> |
                        <a href="update_document_status.php?id=<?= $row['id'] ?>&status=Declined">Decline</a>
                    <?php } ?>
                </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <script>
        function filterTable() {
            const input = document.getElementById("searchBar").value.toLowerCase();
            const rows = document.querySelectorAll("#documentsTable tbody tr");
            rows.forEach(row => {
                const match = row.innerText.toLowerCase().includes(input);
                row.style.display = match ? "" : "none";
            });
        }

        let btn = document.querySelector('#btn');
            let sidebar = document.querySelector('.sidebar');

            btn.onclick = function () {
                sidebar.classList.toggle('active');
            };
    </script>
</body>
</html>
