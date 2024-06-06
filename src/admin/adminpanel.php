<!-- adminpanel.php -->
<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect the user to the login page if not logged in
    header("Location: ../../../index.php");
    exit();
}

// Check if the user has the required role (Role 2)
if ($_SESSION['RoleID'] != 2) {
    // Redirect the user to another appropriate page (e.g., access denied page)
    header("Location: ../../../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="../icons/hmis.ico"> 
    <link rel="stylesheet" href="../../style/adminpanel.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../icons/hmis.ico" alt="HMIS LOGO" class="logo">
            <h2>Admin Panel</h2>
        </div>
        <ul class="sidebar-menu">
        <li><a href="adminpanel.php?page=home.php">Home</a></li>
    <li><a href="adminpanel.php?page=add_user.php">Add users</a></li>
            <li><a href="adminpanel.php?page=add_position.php">Add a position</a></li>
            <li><a href="adminpanel.php?page=add_edit_candidate.php">Add/Edit Candidate</a></li>
            <li><a href="adminpanel.php?page=history.php">View History Log</a></li>
            <li><a href="adminpanel.php?page=view_students.php">View Students</a></li>
            <li><a href="adminpanel.php?page=help_desk_admin.php">Help Desk</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- Main content goes here -->
        <?php   $page = isset($_GET['page']) ? $_GET['page'] : 'home.php';

// Include the requested page
include_once $page; ?>
        
    </div>
    <!--<script src="../js/adminpanel.js"></script>-->
</body>
</html>
