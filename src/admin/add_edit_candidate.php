<?php
// Start the session
//session_start();

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
require_once '../configuration/config.php'; // Adjust the path as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define upload directory
$uploadDir = "../../public/candidate/images/";

// Check if delete candidate button is clicked
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $candidateId = $_GET['delete'];
    
    // Delete candidate from database
    $deleteSql = "DELETE FROM candidates WHERE id = '$candidateId'";
    if ($conn->query($deleteSql) === TRUE) {
        echo "Candidate deleted successfully";
    } else {
        echo "Error deleting candidate: " . $conn->error;
    }
}

// Check if edit candidate form is submitted
if (isset($_POST['edit_candidate'])) {
    $candidateId = $_POST['candidate_id'];
    $fullName = $_POST['full_name'];
    $grade = $_POST['grade'];
    $positionId = $_POST['position'];
    $partyList = $_POST['party_list'];

    // Update candidate details in the database
    $updateSql = "UPDATE candidates SET full_name = '$fullName', grade = '$grade', position_id = '$positionId', party_list = '$partyList'";
    
    // Check if a new picture is uploaded
    if (isset($_FILES['picture_edit']) && $_FILES['picture_edit']['size'] > 0) {
        $pictureName = $_FILES['picture_edit']['name'];
        $pictureTempName = $_FILES['picture_edit']['tmp_name'];
        $picturePath = $uploadDir . $pictureName;

        // Move the uploaded picture to the upload directory
        if (move_uploaded_file($pictureTempName, $picturePath)) {
            $updateSql .= ", picture = '$pictureName'";
        } else {
            echo "Error uploading new picture";
        }
    }

    $updateSql .= " WHERE id = '$candidateId'";

    if ($conn->query($updateSql) === TRUE) {
        echo "Candidate updated successfully";
    } else {
        echo "Error updating candidate: " . $conn->error;
    }
}


// Check if add candidate form is submitted
if (isset($_POST['add_candidate'])) {
    $fullName = $_POST['full_name'];
    $grade = $_POST['grade'];
    $positionId = $_POST['position'];
    $partyList = $_POST['party_list'];

    // File upload process
    $pictureName = $_FILES['picture']['name'];
    $pictureTempName = $_FILES['picture']['tmp_name'];
    $pictureType = $_FILES['picture']['type'];
    $picturePath = $uploadDir . $pictureName;

    // Move the uploaded picture to the upload directory
    if (move_uploaded_file($pictureTempName, $picturePath)) {
        // Insert candidate details into the database
        $insertSql = "INSERT INTO candidates (full_name, grade, position_id, party_list, picture) 
                      VALUES ('$fullName', '$grade', '$positionId', '$partyList', '$pictureName')";

        if ($conn->query($insertSql) === TRUE) {
            echo "New candidate added successfully";
        } else {
            echo "Error adding candidate: " . $conn->error;
        }
    } else {
        echo "Error uploading picture";
    }
}
?>
<link rel="stylesheet" href="../../style/style.css">
<body>
    <h1>Add/Edit Candidate</h1>
    <?php
    // Fetch positions from positions table
    $sql = "SELECT id, position_name FROM positions";
    $result = $conn->query($sql);
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="full_name">Full Name:</label><br>
        <input type="text" id="full_name" name="full_name" required><br><br>

        <label for="grade">Grade:</label><br>
        <select id="grade" name="grade" required>
            <option value="">Select Grade</option>
            <?php
            for ($i = 1; $i <= 12; $i++) {
                echo "<option value='$i'>Grade $i</option>";
            }
            ?>
        </select>

        <label for="position">Position:</label><br>
        <select id="position" name="position" required>
            <option value="">Select Position</option>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['position_name'] . "</option>";
                }
            } else {
                echo "<option value=''>No positions available</option>";
            }
            ?>
        </select><br><br>

        <label for="party_list">Party List:</label><br>
        <input type="text" id="party_list" name="party_list" required><br><br>

        <label for="picture">Upload Picture:</label><br>
        <input type="file" id="picture" name="picture" accept="image/*" required><br><br>

        <input type="submit" name="add_candidate" value="Add Candidate">
    </form>

    <h2>Candidate List</h2>
    <?php
    // Fetch candidates from candidates table
    $candidatesSql = "SELECT id, full_name, grade, position_id, party_list, picture FROM candidates";
    $candidatesResult = $conn->query($candidatesSql);

    if ($candidatesResult->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Full Name</th><th>Grade</th><th>Position</th><th>Party List</th><th>Picture</th><th>Actions</th></tr>";
        while($row = $candidatesResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['full_name'] . "</td>";
            echo "<td>" . $row['grade'] . "</td>";
            // Fetch position name based on position_id
            $positionId = $row['position_id'];
            $positionNameSql = "SELECT position_name FROM positions WHERE id = '$positionId'";
            $positionResult = $conn->query($positionNameSql);
            $positionName = ($positionResult->num_rows > 0) ? $positionResult->fetch_assoc()['position_name'] : "Unknown";
            echo "<td>" . $positionName . "</td>";
            echo "<td>" . $row['party_list'] . "</td>";
            echo "<td><img src='" . $uploadDir . $row['picture'] . "' height='50'></td>";
            echo "<td><a href='adminpanel.php?page=add_edit_candidate.php&edit=" . $row['id'] . "'>Edit</a> | <a href='adminpanel.php?page=add_edit_candidate.php&delete=" . $row['id'] . "' style='text-decoration: none; color: red;' onclick=\"return confirm('Are you sure you want to delete this position?');\">Delete</a></td>";

            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No candidates found";
    }
    ?>

    <?php
    // Edit candidate section
    if (isset($_GET['edit']) && !empty($_GET['edit'])) {
        $candidateId = $_GET['edit'];

        // Fetch candidate details from database
        $editSql = "SELECT id, full_name, grade, position_id, party_list FROM candidates WHERE id = '$candidateId'";
        $editResult = $conn->query($editSql);

        if ($editResult->num_rows > 0) {
            $editRow = $editResult->fetch_assoc();
            ?>
<h2>Edit Candidate</h2>
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="candidate_id" value="<?php echo $editRow['id']; ?>">
    <label for="full_name_edit">Full Name:</label><br>
    <input type="text" id="full_name_edit" name="full_name" value="<?php echo $editRow['full_name']; ?>" required><br><br>

    <label for="grade_edit">Grade:</label><br>
    <select id="grade_edit" name="grade" required>
        <?php
        for ($i = 1; $i <= 12; $i++) {
            $selected = ($editRow['grade'] == $i) ? 'selected' : '';
            echo "<option value='$i' $selected>Grade $i</option>";
        }
        ?>
    </select>

    <label for="position_edit">Position:</label><br>
    <select id="position_edit" name="position" required>
        <?php
        $positionsSql = "SELECT id, position_name FROM positions";
        $positionsResult = $conn->query($positionsSql);
        while($row = $positionsResult->fetch_assoc()) {
            $selected = ($editRow['position_id'] == $row['id']) ? 'selected' : '';
            echo "<option value='" . $row['id'] . "' $selected>" . $row['position_name'] . "</option>";
        }
        ?>
    </select><br><br>

    <label for="party_list_edit">Party List:</label><br>
    <input type="text" id="party_list_edit" name="party_list" value="<?php echo $editRow['party_list']; ?>" required><br><br>

    <label for="picture_edit">Update Picture:</label><br>
    <input type="file" id="picture_edit" name="picture_edit" accept="image/*"><br><br>

    <input type="submit" name="edit_candidate" value="Update Candidate">
</form>

            <?php
        } else {
            echo "Candidate not found";
        }
    }
    ?>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

