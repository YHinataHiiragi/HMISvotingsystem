<?
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .card {
            width: 270px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-right: 10px;
            margin: 10px;
        }

        .main-content {
            margin-left: 120px;
            display: grid;
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-body h3 {
            margin: 0;
            font-size: 36px;
            color: #333;
        }

.main-content {
    display: flex;
    flex-wrap: wrap;
}

.card-graph {
    width: calc(50% - 20px); /* Adjust as needed for spacing */
    margin: 10px;
}

.card-graph .card-header {
    background-color: #007bff;
    color: #fff;
    padding: 10px;
    text-align: center;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.card-graph .card-body {
    padding: 20px;
}

.vote-graph-container {
    width: 100%;
    height: 20px; /* Adjust height as needed */
    background-color: #f0f0f0;
    border-radius: 5px;
    margin-bottom: 5px;
}

.vote-graph {
    height: 100%;
    background-color: #007bff; /* Color of the graph */
    border-radius: 5px;
}

.candidate-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.candidate-info img {
    width: 50px; /* Adjust image size as needed */
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
}

.candidate-name {
    font-weight: bold;
}

.vote-count {
    margin-left: auto;
}

    </style>
<div class="main-content">
        <div class="card">
            <div class="card-header">
                Total Users
            </div>
            <div class="card-body">
                <?php
                require_once '../configuration/config.php';

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                
                // Query to fetch the number of users with RoleID 1
                $sql = "SELECT COUNT(*) AS user_count FROM users WHERE RoleID = 1";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $userCountWithRole1 = $row['user_count'];
                } else {
                    $userCountWithRole1 = 0;
                }
                
                $conn->close();
                    // Fetch the user count from the backend (replace this with your actual logic)
                    //$userCount = 100; // Example user count
                    echo "<h3>$userCountWithRole1 </h3>";
                ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Users Voted Count
            </div>
            <div class="card-body">
            <?php
                require_once '../configuration/config.php';

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch the count of users who have voted
                $sql = "SELECT COUNT(DISTINCT user_id) AS voted_count FROM user_votes";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $votedCount = $row['voted_count'];
                } else {
                    $votedCount = 0;
                }

                $conn->close();
                echo "<h3>$votedCount</h3>";
                ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Elementary Students
            </div>
            <div class="card-body">
            <?php
                require_once '../configuration/config.php';

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch the count of users with grades 1-6 (Elementary)
                $sql = "SELECT COUNT(*) AS elementary_count FROM users WHERE Grade BETWEEN 1 AND 6";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $elementaryCount = $row['elementary_count'];
                } else {
                    $elementaryCount = 0;
                }

                $conn->close();
                echo "<h3>$elementaryCount</h3>";
                ?>
    
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                High School Students
            </div>
            <div class="card-body">
            <?php
                require_once '../configuration/config.php';

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to fetch the count of users with grades 1-6 (Elementary)
                $sql = "SELECT COUNT(*) AS elementary_count FROM users WHERE Grade BETWEEN 7 AND 12";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $elementaryCount = $row['elementary_count'];
                } else {
                    $elementaryCount = 0;
                }

                $conn->close();
                echo "<h3>$elementaryCount</h3>";
                ?>
    
            </div>
        </div>
    </div>  
        <?php
//tally.php
// Assuming you have established a database connection
require_once '../configuration/config.php';
$conn = new mysqli($servername, $username, $password, $dbname);
function fetchVoteCountByPositionAndGrade() {
    global $conn;
    $voteCount = array();

    // Fetch grades
    $gradeQuery = "SELECT grade FROM candidates GROUP BY grade";
    $gradeResult = mysqli_query($conn, $gradeQuery);

    while ($gradeRow = mysqli_fetch_assoc($gradeResult)) {
        $grade = $gradeRow['grade'];

        // Determine grade level
        $gradeLevel = ($grade >= 1 && $grade <= 6) ? 'Elementary' : 'High School';

        // Fetch positions
        $positionQuery = "SELECT p.id AS position_id, p.position_name, c.id AS candidate_id, c.full_name, c.picture AS candidate_picture, IFNULL(vc.vote_count, 0) AS vote_count
        FROM positions p
        LEFT JOIN candidates c ON p.id = c.position_id
        LEFT JOIN vote_counts vc ON c.id = vc.candidate_id
        WHERE c.grade = '$grade'
        ORDER BY p.id ASC";

        $positionResult = mysqli_query($conn, $positionQuery);

        while ($positionRow = mysqli_fetch_assoc($positionResult)) {
            $positionID = $positionRow['position_id'];
            $positionName = $positionRow['position_name'];

            $candidateID = $positionRow['candidate_id'];
            $candidateName = $positionRow['full_name'];
            $candidatePicture = $positionRow['candidate_picture'];
            $voteCountValue = $positionRow['vote_count'];

            // Add data to the vote count array
            $voteCount[$gradeLevel][$positionName][$candidateName] = array(
                'picture' => $candidatePicture,
                'vote_count' => $voteCountValue
            );
        }
    }

    return $voteCount;
}

// Fetch vote count for each candidate, grouped by position and grade
$voteCountByPositionAndGrade = fetchVoteCountByPositionAndGrade();
?>
<div class="main-content" style="display: flex; justify-content: space-between;">
    <div class="card" style="width: calc(50% - 20px); margin: 10px;">
        <div class="card-header" style="background-color: #007bff; color: #fff; padding: 10px; text-align: center; border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <h2>High School Positions</h2>
        </div>
        <div class="card-body" style="padding: 20px;">
            <?php
            // Default to the last position if position name is not provided in the URL or invalid
            $lastPosition = array_key_last($voteCountByPositionAndGrade['High School']);
            $positionName = isset($_GET['positionName']) && array_key_exists($_GET['positionName'], $voteCountByPositionAndGrade['High School']) ? $_GET['positionName'] : $lastPosition;
            $candidates = $voteCountByPositionAndGrade['High School'][$positionName];
            ?>
            <h3><?php echo $positionName; ?></h3>
            <?php foreach ($candidates as $candidateName => $candidateData) : ?>
                <div class="candidate-info" style="display: flex; align-items: center; margin-bottom: 10px;">
                    <!-- Graph representing vote count -->
                    <div class="vote-graph" style="height: 20px; background-color: #007bff; border-radius: 5px; width: <?php echo min($candidateData['vote_count'] * 5, 100) . 'px'; ?>;"></div>
                    <img src="../../public/candidate/images/<?php echo $candidateData['picture']; ?>" alt="<?php echo $candidateName; ?>" style="width: 50px; height: 50px; border-radius: 50%; margin-left: 10px; margin-right: 10px;">
                    <span class="candidate-name" style="font-weight: bold;"><?php echo $candidateName; ?></span>
                    <span class="vote-count" style="margin-left: auto;"><?php echo $candidateData['vote_count']; ?> votes</span>
                </div>
            <?php endforeach; ?>
            
            <div class="position-buttons" style="display: flex;">
                <?php foreach (array_reverse($voteCountByPositionAndGrade['High School']) as $position => $candidates) : ?>
                    <form method="GET">
                        <input type="hidden" name="positionName" value="<?php echo $position; ?>">
                        <button type="submit" style="background-color: #007bff; color: #fff; border: none; border-radius: 8px; padding: 5px 5px; margin-right: 5px; margin-top: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.3s ease;"><?php echo $position; ?></button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card" style="width: calc(50% - 20px); margin: 10px;">
        <div class="card-header" style="background-color: #007bff; color: #fff; padding: 10px; text-align: center; border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <h2>Elementary Positions</h2>
        </div>
        <div class="card-body" style="padding: 20px;">
            <?php
            // Default to the last position if position name is not provided in the URL or invalid
            $lastPosition = array_key_last($voteCountByPositionAndGrade['Elementary']);
            $positionName = isset($_GET['positionName']) && array_key_exists($_GET['positionName'], $voteCountByPositionAndGrade['Elementary']) ? $_GET['positionName'] : $lastPosition;
            $candidates = $voteCountByPositionAndGrade['Elementary'][$positionName];
            ?>
            <h3><?php echo $positionName; ?></h3>
            <?php foreach ($candidates as $candidateName => $candidateData) : ?>
                <div class="candidate-info" style="display: flex; align-items: center; margin-bottom: 10px;">
                    <!-- Graph representing vote count -->
                    <div class="vote-graph" style="height: 20px; background-color: #007bff; border-radius: 5px; width: <?php echo min($candidateData['vote_count'] * 5, 100) . 'px'; ?>;"></div>
                    <img src="../../public/candidate/images/<?php echo $candidateData['picture']; ?>" alt="<?php echo $candidateName; ?>" style="width: 50px; height: 50px; border-radius: 50%; margin-left: 10px; margin-right: 10px;">
                    <span class="candidate-name" style="font-weight: bold;"><?php echo $candidateName; ?></span>
                    <span class="vote-count" style="margin-left: auto;"><?php echo $candidateData['vote_count']; ?> votes</span>
                </div>
            <?php endforeach; ?>
            
            <div class="position-buttons" style="display: flex;">
                <?php foreach (array_reverse($voteCountByPositionAndGrade['Elementary']) as $position => $candidates) : ?>
                    <form method="GET">
                        <input type="hidden" name="positionName" value="<?php echo $position; ?>">
                        <button type="submit" style="background-color: #007bff; color: #fff; border: none; border-radius: 8px; padding: 5px 5px; margin-right: 5px; margin-top: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.3s ease;"><?php echo $position; ?></button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>



    </div>