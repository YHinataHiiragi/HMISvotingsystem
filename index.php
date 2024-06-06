<?php
//login.php
// Start session
session_start();

// Include config file
require_once "src/configuration/config.php";

// Check if user is already logged in, redirect them to appropriate page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if ($_SESSION["RoleID"] == 1) {
        header("location: src/user/dashboard.php");
    } elseif ($_SESSION["RoleID"] == 2) {
        header("location: src/admin/adminpanel.php");
    }
    exit;
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT UserID, Username, Password, RoleID FROM users WHERE Username = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $UserID, $username, $hashed_password, $RoleID);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Log the login event
                            $logLoginQuery = "INSERT INTO login_logs (user_id) VALUES (?)";
                            $stmt = $conn->prepare($logLoginQuery);
                            $stmt->bind_param("i", $UserID);
                            $stmt->execute();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["UserID"] = $UserID;
                            $_SESSION["username"] = $username;
                            $_SESSION["RoleID"] = $RoleID;

                            // Redirect user to appropriate page based on role
                            if ($RoleID == 1) {
                                header("location: src/user/dashboard.php");
                            } elseif ($RoleID == 2) {
                                header("location: src/admin/adminpanel.php");
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Voting</title>
    <link rel="icon" type="image/x-icon" href="src/icons/hmis.ico"> 
    <link rel="stylesheet" href="style/index.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <img src="src/images/hmis.png" alt="HMIS LOGO" class="logo">
            <div class="headername">
                <h1>Student Government Council</h1>
                <h3>Digital Voting System</h3>
            </div>
            <button class="live-tally-btn" onclick="location.href='live.php'">Live tally</button>
        </div>
    </div>
    <div class="login-container">
        <div class="login">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="text" name="username" placeholder="Username" class="login-input <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                <input type="password" name="password" placeholder="Password" class="login-input <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
    <footer style="text-align: center; padding: 0px;">
    <span style="margin-bottom: 1px; text-align: center; font-size: 10px">&copy; 2024 Harvesters' Missions International School. All rights reserved.</span><br>
    <span style="margin-top: 1px; text-align: center; font-size: 10px">Developed by Hanz Eduard Maclan, IT</span>
    </footer>
</body>
</html>
