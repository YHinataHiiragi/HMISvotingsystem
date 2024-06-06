<!-- add_user.php -->
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
require_once '../configuration/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
    $lrn = $_POST['lrn'];
    $fullname = $_POST['fullname'];
    $grade = $_POST['grade'];
    $role = 1; // Always set role to 1 (student)

    $sql = "INSERT INTO users (Username, Password, LRN, FullName, Grade, RoleID) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $username, $password, $lrn, $fullname, $grade, $role);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<h1 style="text-align: center;">Add User</h1>
<form action="adminpanel.php?page=add_user.php" method="post" style="max-width: 400px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">
    <div style="margin-bottom: 15px;">
        <label for="username" style="display: block; font-weight: bold;">Username:</label>
        <input type="text" id="username" name="username" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="password" style="display: block; font-weight: bold;">Password:</label>
        <input type="password" id="password" name="password" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="lrn" style="display: block; font-weight: bold;">LRN (Number):</label>
        <input type="text" id="lrn" name="lrn" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="fullname" style="display: block; font-weight: bold;">Full Name:</label>
        <input type="text" id="fullname" name="fullname" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
    </div>
    <div style="margin-bottom: 15px;">
        <label for="grade" style="display: block; font-weight: bold;">Grade:</label>
        <select id="grade" name="grade" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
            <option value="">Select Grade</option>
            <?php
            for ($i = 1; $i <= 12; $i++) {
                echo "<option value='$i'>Grade $i</option>";
            }
            ?>
        </select>
    </div>
    <div>
        <input type="submit" value="Add User" style="width: 100%; padding: 10px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
    </div>
</form>
