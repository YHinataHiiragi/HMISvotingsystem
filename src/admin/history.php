<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.edit-log').click(function(){
                var logID = $(this).data('id');
                $.ajax({
                    url: 'fetch_log.php',
                    type: 'GET',
                    data: {log_id: logID},
                    success: function(response){
                        var log = JSON.parse(response);
                        $('#logID').val(log.log_id);
                        $('#userID').val(log.user_id);
                        $('#candidateID').val(log.candidate_id);
                        $('#positionID').val(log.position_id);
                        $('#oldVoteCount').val(log.old_vote_count);
                        $('#newVoteCount').val(log.new_vote_count);
                        $('#editLogModal').modal('show');
                    }
                });
            });

            $('#saveChanges').click(function(){
                var logID = $('#logID').val();
                var newVoteCount = $('#newVoteCount').val();
                $.ajax({
                    url: 'update_log.php',
                    type: 'POST',
                    data: {log_id: logID, new_vote_count: newVoteCount},
                    success: function(response){
                        alert(response);
                        location.reload();
                    }
                });
            });
        });
    </script>
</head>
<body>
<div class="container">
    <h2>Change History</h2>
    <?php
require_once '../configuration/config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get all change logs with full user and candidate names
$sqlChangeLogs = "SELECT cl.log_id, u.FullName as user_name, c.full_name as candidate_name, cl.position_id, cl.old_vote_count, cl.new_vote_count, cl.changed_at
                 FROM change_log cl
                 INNER JOIN users u ON cl.user_id = u.UserID
                 INNER JOIN candidates c ON cl.candidate_id = c.id";
$resultChangeLogs = $conn->query($sqlChangeLogs);

// Query to get all login logs with full user name
$sqlLoginLogs = "SELECT ll.log_id, u.FullName as user_name, '-' as candidate_name, '-' as position_id, '-' as old_vote_count, '-' as new_vote_count, ll.login_time as changed_at
                FROM login_logs ll
                INNER JOIN users u ON ll.user_id = u.UserID";
$resultLoginLogs = $conn->query($sqlLoginLogs);

// Check if there are change logs or login logs
if ($resultChangeLogs->num_rows > 0 || $resultLoginLogs->num_rows > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Log ID</th><th>User Name</th><th>Candidate Name</th><th>Position ID</th><th>Old Vote Count</th><th>New Vote Count</th><th>Changed At</th><th>Action</th></tr></thead>";
    echo "<tbody>";

    // Output change logs
    while($row = $resultChangeLogs->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["log_id"] . "</td>";
        echo "<td>" . $row["user_name"] . "</td>";
        echo "<td>" . $row["candidate_name"] . "</td>";
        echo "<td>" . $row["position_id"] . "</td>";
        echo "<td>" . $row["old_vote_count"] . "</td>";
        echo "<td>" . $row["new_vote_count"] . "</td>";
        echo "<td>" . $row["changed_at"] . "</td>";
        echo "<td><button class='btn btn-primary edit-log' data-id='" . $row["log_id"] . "'>Edit</button></td>";
        echo "</tr>";
    }

    // Output login logs
    while($row = $resultLoginLogs->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["log_id"] . "</td>";
        echo "<td>" . $row["user_name"] . "</td>";
        echo "<td>" . $row["candidate_name"] . "</td>";
        echo "<td>" . $row["position_id"] . "</td>";
        echo "<td>" . $row["old_vote_count"] . "</td>";
        echo "<td>" . $row["new_vote_count"] . "</td>";
        echo "<td>" . $row["changed_at"] . "</td>";
        echo "<td>-</td>"; // Placeholder for non-applicable action
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "No change or login logs found.";
}

$conn->close();
?>

</div>

<!-- Modal -->
<div class="modal fade" id="editLogModal" tabindex="-1" role="dialog" aria-labelledby="editLogModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLogModalLabel">Edit Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLogForm">
                    <input type="hidden" id="logID">
                    <div class="form-group">
                        <label for="userID">User ID</label>
                        <input type="text" class="form-control" id="userID" disabled>
                    </div>
                    <div class="form-group">
                        <label for="candidateID">Candidate ID</label>
                        <input type="text" class="form-control" id="candidateID" disabled>
                    </div>
                    <div class="form-group">
                        <label for="positionID">Position ID</label>
                        <input type="text" class="form-control" id="positionID" disabled>
                    </div>
                    <div class="form-group">
                        <label for="oldVoteCount">Old Vote Count</label>
                        <input type="text" class="form-control" id="oldVoteCount" disabled>
                    </div>
                    <div class="form-group">
                        <label for="newVoteCount">New Vote Count</label>
                        <input type="number" class="form-control" id="newVoteCount">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>

