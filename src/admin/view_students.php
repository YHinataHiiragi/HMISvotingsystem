<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Students</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.view-history').click(function(){
                var userID = $(this).data('id');
                $.ajax({
                    url: 'view_history.php',
                    type: 'GET',
                    data: {UserID: userID},
                    success: function(response){
                        $('#modalBody').html(response);
                        $('#historyModal').modal('show');
                    }
                });
            });

            // Search functionality
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#studentsTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</head>
<body>
<div class="container">
    <h2>List of Students</h2>
    <input class="form-control mb-4" id="searchInput" type="text" placeholder="Search for students...">

    <?php
    require_once '../configuration/config.php';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get all students (users with RoleID 1)
    $sql = "SELECT UserID, Username, FullName, Grade FROM users WHERE RoleID = 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Username</th><th>FullName</th><th>Grade</th><th>Action</th></tr></thead>";
        echo "<tbody id='studentsTable'>";

        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["Username"] . "</td>";
            echo "<td>" . $row["FullName"] . "</td>";
            echo "<td>" . $row["Grade"] . "</td>";
            echo "<td><button class='btn btn-primary view-history' data-id='" . $row["UserID"] . "'>View History</button></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "No students found.";
    }

    $conn->close();
    ?>
</div>

<!-- Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Voting History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Voting history will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
