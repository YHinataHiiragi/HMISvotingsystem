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

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Function to sanitize user input
    function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Add or update position
    if(isset($_POST['submit'])) {
        $position_name = sanitize_input($_POST['position_name']);
        if(!empty($_POST['position_id'])) {
            // Update existing position
            $position_id = $_POST['position_id'];
            $sql = "UPDATE positions SET position_name=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $position_name, $position_id);
            $action = "updated";
        } else {
            // Add new position
            $sql = "INSERT INTO positions (position_name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $position_name);
            $action = "added";
        }
        if ($stmt->execute() === TRUE) {
            echo "<p style='color: green;'>Position $action successfully</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }
        $stmt->close();
    }

    // Delete position
    if(isset($_GET['delete'])) {
        $delete_id = $_GET['delete'];
        $sql = "DELETE FROM positions WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute() === TRUE) {
            echo "<p style='color: green;'>Position deleted successfully</p>";
        } else {
            echo "<p style='color: red;'>Error deleting position: " . $conn->error . "</p>";
        }
        $stmt->close();
    }

    // Fetch positions from database
    $sql = "SELECT id, position_name FROM positions";
    $result = $conn->query($sql);
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add/Edit/Delete Position</title>
</head>
<link rel="stylesheet" href="../../style/style.css">
<body>
    <h2>Add/Edit/Delete Position</h2>
    <form method="post">
        <input type="hidden" name="position_id" value="">
        <label for="position_name" style="margin-right: 10px;">Position Name:</label>
        <input type="text" id="position_name" name="position_name" value="" style="margin-bottom: 10px;"><br>
        <input type="submit" name="submit" value="Add/Edit Position" style="padding: 5px 10px;">
    </form>

    <h2>Existing Positions</h2>
    <table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse;">
        <tr>
            <th style="padding: 5px;">Position ID</th>
            <th style="padding: 5px;">Position Name</th>
            <th style="padding: 5px;">Edit</th>
            <th style="padding: 5px;">Delete</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='padding: 5px;'>".$row["id"]."</td>";
                echo "<td style='padding: 5px;'>".$row["position_name"]."</td>";
                echo "<td style='padding: 5px;'><a href='adminpanel.php?page=add_position.php&edit=".$row["id"]."' style='text-decoration: none; color: blue;'>Edit</a></td>";
                echo "<td style='padding: 5px;'><a href='adminpanel.php?page=add_position.php&delete=".$row["id"]."' style='text-decoration: none; color: red;' onclick=\"return confirm('Are you sure you want to delete this position?');\">Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='padding: 5px;'>No positions found</td></tr>";
        }
        ?>
    </table>

    <?php
    // Handle edit request
    if(isset($_GET['edit'])) {
        $edit_id = $_GET['edit'];
        $sql = "SELECT id, position_name FROM positions WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
            <script>
                document.getElementById("position_name").value = "<?php echo $row['position_name']; ?>";
                document.getElementsByName("position_id")[0].value = "<?php echo $row['id']; ?>";
                document.getElementsByName("submit")[0].value = "Update Position";
                window.location.hash = 'position_name'; // Scroll to the position name input field after editing
            </script>
            <?php
        }
        $stmt->close();
    }

    // Close connection
    $conn->close();
    ?>
</body>
</html>

