<?php
//help_desk_student.php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

require_once '../configuration/config.php';

// Fetch user's information from the database
$userID = $_SESSION['UserID'];
$query = "SELECT * FROM users WHERE UserID = $userID";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$user_id = $_SESSION['UserID'];

// Function to fetch messages for the user
function fetchMessages($user_id, $conn) {
    $sql = "SELECT m.*, 
                   u.FullName AS user_fullname,
                   a.FullName AS admin_fullname
            FROM help_desk_messages m 
            LEFT JOIN users u ON m.user_id = u.UserID
            LEFT JOIN users a ON m.admin_id = a.UserID
            WHERE user_id = ? 
            ORDER BY m.timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    return $messages;
}

// Handle sending a message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message = $_POST['message'];
    $sql = "INSERT INTO help_desk_messages (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    // Redirect back to the same page after sending the message
    header("Location: help_desk_student.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Help Desk</title>
    <link rel="icon" type="image/x-icon" href="../icons/help.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<style>
    /* style.css */

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.header {
    background-color: #f8f9fa;
    padding: 10px;
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between; /* To push the elements to the edges */
}
.logo {
    width: 70px;
    height: 70px;
    margin-right: 10px;
}

.headername {
    flex-grow: 1; /* To allow it to grow and take up remaining space */
    margin-right: auto; /* Pushes the logout button to the right */
}

.headname h4, .headname h5 {
    display: block;
    margin: 0;
    margin-right: 100px;
}

.headername h1, .headername h3 {
    margin: 0; /* Remove default margin */
}

.logout-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 8px;
    vertical-align: middle;
}

.logout-btn:hover {
    background-color: #0056b3;
}



label {
    display: block;
    margin-bottom: 10px;
    font-size: 16px;
    color: #555;
}


img {
    width: 100px;
    height: 100px;
    margin-left: 10px;
    vertical-align: middle;
}


    </style>
<body>
<div class="header">
    
    <div class="header-content">

        <img src="../images/hmis.png" alt="HMIS LOGO" class="logo">
        <div class="headername">
            <h1>Help Desk</h1>
        </div>
        <div class="headname">
            <h4>Welcome, <?php echo $user['FullName']; ?>!</h4>
            <h5>Grade: <?php echo $user['Grade']; ?></h5>
        </div>
        <a href="../logout.php"><button class="logout-btn">Logout</button></a>
    </div>
</div>
<a href="dashboard.php" style="display: inline-block; text-decoration: none; color: #333; font-size: 16px; font-weight: bold; margin-left: 20px;">
    <span style="vertical-align: middle;">&#9664;</span> Back to Dashboard
</a>

<div class="container">
    <h1>User Help Desk</h1>
    <div id="chat" style="height: 300px; overflow-y: scroll;">
        <?php
        // Fetch messages for the user
        $messages = fetchMessages($user_id, $conn);
        foreach ($messages as $message) {
            // Output each message with the appropriate sender's full name
            $senderFullName = ($message['admin_id'] !== null) ? htmlspecialchars($message['admin_fullname']) : htmlspecialchars($message['user_fullname']);
            $messageContent = htmlspecialchars($message['message']);
            echo "<p><strong>$senderFullName:</strong> $messageContent</p>";
        }
        ?>
    </div>
    <form id="messageForm">
        <div class="form-group">
            <textarea class="form-control" rows="2" id="message" name="message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>

<footer class="text-center" style="padding: 0px;">
    <span style="font-size: 10px;" class="mb-0">&copy; 2024 Harvesters' Missions International School. All rights reserved.</span><br>
    <span style="font-size: 10px;" class="mb-0">Developed by Hanz Eduard Maclan, IT</span>
</footer>




<script>

$(document).ready(function(){
    var isFirstLoad = true; // Flag to track first load

    // Function to fetch new messages
    function fetchNewMessages(){
        $.ajax({
            url: "fetch_messages.php", // Change this to your PHP file that fetches messages
            method: "GET",
            success: function(data){
                $("#chat").html(data); // Replace the chat content with the new messages
                if (isFirstLoad) {
                    scrollToBottom(); // Scroll to the bottom of the chat div only on first load
                    isFirstLoad = false; // Set the flag to false after first load
                }
            }
        });
    }

    // Function to scroll to the bottom of the chat div
    function scrollToBottom(){
        var chatDiv = document.getElementById("chat");
        chatDiv.scrollTop = chatDiv.scrollHeight;
    }

    // Fetch new messages every 5 seconds
    setInterval(fetchNewMessages, 100);

    // Call fetchNewMessages function on page load
    fetchNewMessages();

    // Submit form using AJAX
    $("#messageForm").submit(function(e){
        e.preventDefault(); // Prevent default form submission
        var formData = $(this).serialize(); // Serialize form data
        $.ajax({
            url: "help_desk_student.php", // Change this to your PHP file that sends messages
            method: "POST",
            data: formData,
            success: function(response){
                fetchNewMessages(); // Fetch new messages after sending a message
                $("#message").val(""); // Clear the message input field
            }
        });
    });
});
</script>


</body>
</html>

