<?php
require_once '../configuration/config.php'; // Assuming you have already established a database connection

// Check if the user is logged in
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}


// Fetch user's information from the database
$userID = $_SESSION['UserID'];
$query = "SELECT * FROM users WHERE UserID = $userID";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if the user has already voted
$query = "SELECT COUNT(*) as user_id FROM user_votes WHERE user_id = $userID";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
if ($row['user_id'] > 0) {
    header("Location: already_vt.php");
    exit();
}


// Determine the grade range for candidates based on the user's grade
if ($user['Grade'] >= 1 && $user['Grade'] <= 6) {
    $gradeCondition = "WHERE c.grade BETWEEN 1 AND 6";
} else {
    $gradeCondition = "WHERE c.grade BETWEEN 7 AND 12";
}

// Fetch candidates based on the user's grade and group them by position
$query = "SELECT p.position_name, c.id, c.full_name, c.picture
          FROM positions p
          LEFT JOIN candidates c ON p.id = c.position_id
          $gradeCondition
          ORDER BY p.id";
$result = mysqli_query($conn, $query);

// Check if the query executed successfully
if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Store candidates grouped by positions in an associative array
$positions = [];
while ($row = mysqli_fetch_assoc($result)) {
    $positions[$row['position_name']][] = ['id' => $row['id'], 'full_name' => $row['full_name'], 'picture' => $row['picture']];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Ballot</title>
    <link rel="stylesheet" href="style.css">
    <!-- Add your CSS stylesheets or link to a CSS file here -->
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            text-align: center;
        }
        .modal-buttons {
            margin-top: 20px;
        }
        /* Button styles */
        .modal-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
        }
        .modal-button:hover {
            background-color: #0056b3;
        }
        /* Other styles */
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="header">
    
    <div class="header-content">

        <img src="../images/hmis.png" alt="HMIS LOGO" class="logo">
        <div class="headername">
            <h1>Student Government Council</h1>
            <h3>Digital Voting System</h3>
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

<!-- Modal -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to submit your vote? Once submitted, it cannot be undone.</p>
        <div class="modal-buttons">
            <button class="modal-button" onclick="submitVote()">Submit</button>
            <button class="modal-button" onclick="closeModal()"style="background-color: red;">Cancel</button>
        </div>
    </div>
</div>

    <form id="votingForm" action="vote.php" method="post" onsubmit="showModal(event)">
        <?php foreach ($positions as $positionName => $candidates) : ?>
            <div class="position">
                <h3><?php echo $positionName; ?></h3>
                <?php foreach ($candidates as $candidate) : ?>
                    <label>
                        <input type="radio" name="<?php echo $positionName; ?>" value="<?php echo $candidate['id']; ?>">
                        <?php echo $candidate['full_name']; ?>
                        <img src="../../public/candidate/images/<?php echo $candidate['picture']; ?>" alt="<?php echo $candidate['full_name']; ?>">
                    </label><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <input type="submit" value="Vote">
    </form>

    <footer style="text-align: center; padding: 20px;">
    If there is a problem, please report it at the <a href="help_desk_student.php" style="text-decoration: none; color: blue;">Help Desk</a>.<br><br>
    <span style="margin-bottom: 1px; text-align: center; font-size: 10px">&copy; 2024 Harvesters' Missions International School. All rights reserved.</span><br>
    <span style="margin-top: 1px; text-align: center; font-size: 10px">Developed by Hanz Eduard Maclan, IT</span>
    </footer>

    <!-- Add any additional content or styling here -->

    <script>
        // JavaScript functions for modal
        function showModal(event) {
            event.preventDefault();
            var modal = document.getElementById('confirmationModal');
            modal.style.display = 'block';
        }

        function closeModal() {
            var modal = document.getElementById('confirmationModal');
            modal.style.display = 'none';
        }

        function submitVote() {
            document.getElementById('votingForm').submit();
        }
    </script>
</body>
</html>

