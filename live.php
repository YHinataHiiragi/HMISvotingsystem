<?php
//tally.php
// Assuming you have established a database connection
require_once 'src/configuration/config.php';

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tally</title>
    <link rel="icon" type="image/x-icon" href="src/icons/hmis.ico">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('src/images/bg_school.jpg'); /* Replace 'background-image.jpg' with your actual image path */
            background-size: cover;
            background-position: center;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            justify-content: space-between; /* Aligns the left and right columns */
        }
        .grade-level {
            flex: 1; /* Equal width for both columns */
            margin-bottom: 40px;
        }
        .grade-level h2 {
            border-bottom: 3px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 2.5em;
            color: #ddd;
            -webkit-text-stroke: #333;
            text-align: center; /* Center the grade level header */
        }
        .position-card {
            background-color: rgba(255, 255, 255, 0.9); /* Adjust the last value (alpha) to change transparency */
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease-in-out;
        }
        .position-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .position-title {
            font-weight: bold;
            font-size: 1.3em;
            margin-bottom: 20px;
            color: #333;
            text-transform: uppercase;
        }
        .candidate-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .candidate-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
            object-fit: cover;
            border: 2px solid #4CAF50;
        }
        .candidate-name {
            font-weight: bold;
            font-size: 1.1em;
            flex: 1;
        }
        .vote-bar-container {
            width: 70%;
            margin-right: 15px;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            flex-grow: 1;
            position: relative;
        }
        .vote-bar {
            background-color: #4CAF50;
            height: 20px;
            border-radius: 5px;
            transition: width 0.3s ease-in-out;
        }
        .vote-count {
            font-size: 0.9em;
            color: #555;
            text-align: right;
            flex-basis: 50px;
            margin-left: 15px;
        }
        @media (max-width: 600px) {
            .candidate-info {
                flex-direction: column;
                align-items: flex-start;
            }
            .candidate-info img {
                margin-bottom: 10px;
            }
            .vote-bar-container {
                width: 100%;
            }
            .vote-count {
                margin-left: 0;
                text-align: left;
            }
        }
        /* Add style to separate High School and Elementary sections */
        .grade-level {
            border: 5px; 
            padding: 5px;
            border-radius: 10px;
        }
        .grade-level + .grade-level {
            margin-left: 200px; /* Adjust the margin between sections */
        }
    </style>
</head>
<body>
<a href="index.php" style="display: inline-block; text-decoration: none; color: #333; font-size: 16px; font-weight: bold; margin-left: 20px;">
    <span style="vertical-align: middle;">&#9664;</span> Go back
</a>
<div class="container">
    <div class="grade-level">
        <h2>High School</h2>
        <?php foreach (array_reverse($voteCountByPositionAndGrade['High School'], true) as $positionName => $candidates) : ?>
            <div class="position-card">
                <h3 class="position-title"><?php echo $positionName; ?></h3>
                <?php foreach ($candidates as $candidateName => $candidateData) : ?>
                    <div class="candidate-info">
                        <img src="public/candidate/images/<?php echo $candidateData['picture']; ?>" alt="<?php echo $candidateName; ?>">
                        <span class="candidate-name"><?php echo $candidateName; ?></span>
                        <div class="vote-bar-container">
                            <div class="vote-bar" style="width: <?php echo min($candidateData['vote_count'] * 10, 100) . '%'; ?>"></div>
                        </div>
                        <span class="vote-count"><?php echo $candidateData['vote_count']; ?> votes</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="grade-level">
        <h2>Elementary</h2>
        <?php foreach (array_reverse($voteCountByPositionAndGrade['Elementary'], true) as $positionName => $candidates) : ?>
            <div class="position-card">
                <h3 class="position-title"><?php echo $positionName; ?></h3>
                <?php foreach ($candidates as $candidateName => $candidateData) : ?>
                    <div class="candidate-info">
                        <img src="public/candidate/images/<?php echo $candidateData['picture']; ?>" alt="<?php echo $candidateName; ?>">
                        <span class="candidate-name"><?php echo $candidateName; ?></span>
                        <div class="vote-bar-container">
                            <div class="vote-bar" style="width: <?php echo min($candidateData['vote_count'] * 10, 100) . '%'; ?>"></div>
                        </div>
                        <span class="vote-count"><?php echo $candidateData['vote_count']; ?> votes</span>
                    </div>
                    <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

