<!-- help_desk_admin.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Help Desk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<style>
    /* CSS for Admin Help Desk page */
    body {
        font-family: Arial, sans-serif;
    }

    .container {
        margin-top: 50px;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .modal-body {
        max-height: 300px; /* Adjust height as needed */
        overflow-y: auto;
    }

    .modal-header {
        background-color: #007bff;
        color: #fff;
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
        display: block;
    }

    .modal-content {
        border-radius: 0;
    }

    .form-control {
        border-radius: 0;
    }

    .input-group-append {
        padding-left: 10px;
    }

    textarea.form-control {
        resize: none;
    }

    .input-group {
        width: 100%!important;
    }

    /* Adjust modal size as needed */
    .modal-dialog {
        max-width: 800px;
    }

    /* Optional: style for error message */
    .error-message {
        color: #dc3545;
        margin-top: 5px;
    }

</style>
<body>
<div class="container">
    <h1>Admin Help Desk</h1>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../configuration/config.php';

                // Fetch distinct user IDs
                $sql = "SELECT DISTINCT h.user_id, h.status, u.FullName, u.Grade FROM help_desk_messages h JOIN users u ON h.user_id = u.UserID";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $user_id = $row['user_id'];
                        $FullName = $row['FullName'];
                        $Grade = $row['Grade'];
                        $status = $row['status'];
                        if ($Grade >= 1 && $Grade <= 6) {
                            $gradeLabel = "Elementary";
                        } elseif ($Grade >= 7 && $Grade <= 10) {
                            $gradeLabel = "High School";
                        } elseif ($Grade >= 11 && $Grade <= 12) {
                            $gradeLabel = "Senior High School";
                        } else {
                            $gradeLabel = "Unknown";
                        }
                        echo "<tr>";
                        echo "<td>$user_id</td>";
                        echo "<td>$FullName</td>";
                        echo "<td>$Grade ($gradeLabel)</td>";
                        echo "<td>$status</td>";
                        echo "<td>";
                        echo "<button class='btn btn-primary view-messages' data-userid='$user_id'>View Messages</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Messages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Messages fetched via AJAX will be displayed here -->
            </div>
            <div class="modal-footer">
                <form id="sendMessageForm">
                    <input type="hidden" id="userIdInput" name="user_id">
                    <div class="input-group">
                        <textarea class="form-control" id="messageInput" name="message" placeholder="Type your message here..." required></textarea>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </div>
                </form>
                <div class="message">
                    
                    <button class="btn btn-success resolve-message" data-userid="">Resolve</button>
                </div>
                <div id="resolveMessageConfirmation" class="alert alert-success" role="alert" style="display: none;">
                    This help request has been resolved.
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle view messages button click
        $('.view-messages').click(function() {
            var userId = $(this).data('userid');
            // Fetch messages initially when opening the modal
            fetchMessages(userId);
            // Show the modal
            $('#messageModal').modal('show');
            // Set the user ID in the form
            $('#userIdInput').val(userId);
            // Set the user ID in the resolve button
            $('.resolve-message').data('userid', userId);
            // Start long polling
            longPoll(userId);
        });

        // Handle resolve message button click
        $(document).on('click', '.resolve-message', function() {
            var userId = $(this).data('userid');
            console.log("User ID:", userId); // Log user ID
            var button = $(this); // Store reference to the button

            // Call AJAX to update the status of the messages in the database
            $.ajax({
                url: 'resolve_message.php',
                type: 'POST',
                data: { user_id: userId },
                success: function(response) {
                    // Hide the resolved message from the modal
                    button.closest('.message').hide();
                    // Show the confirmation message
                    $('#resolveMessageConfirmation').show();
                    // Fetch updated messages to include the resolution message
                    fetchMessages(userId);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        var pollCount = 0; // Counter to track the number of polls

        // Function to fetch messages from the server
        function fetchMessages(userId) {
            $.ajax({
                url: 'get_messages.php',
                type: 'POST',
                data: { user_id: userId },
                success: function(data) {
                    $('#messageModal .modal-body').html(data);
                    if (pollCount < 3) {
                        scrollToBottom(); // Scroll to bottom for the first three polls
                        pollCount++; // Increment the poll count
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Long polling function
        function longPoll(userId) {
            // Poll for new messages every 3 seconds
            setInterval(function() {
                fetchMessages(userId);
            }, 100);
        }

        // Handle form submission to send message
        $('#sendMessageForm').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: 'get_messages.php', // Update to the correct endpoint
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Display success or error message
                    $('#messageModal .modal-body').html(response);
                    // Clear the message input field
                    $('#messageInput').val('');
                    scrollToBottom(); // Scroll to bottom after sending message
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Function to scroll modal body to bottom
        function scrollToBottom() {
            var $modalBody = $('#messageModal .modal-body');
            $modalBody.scrollTop($modalBody.prop('scrollHeight'));
        }
    });
</script>
</body>
</html>
