<?php
session_start();

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_new_password = $_POST["confirm_new_password"];

    // Retrieve the user's current password from the database
    $sql_user = "SELECT password FROM user WHERE user_id = $user_id";
    $result_user = $conn->query($sql_user);

    if ($result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();
        $current_password_db = $row_user["password"];

        // Verify the current password
        if ($current_password_db == $current_password) {
            // Check if the new password and confirm new password match
            if ($new_password == $confirm_new_password) {
                // Update the user's password in the database
                $sql_update_password = "UPDATE user SET password = '$new_password' WHERE user_id = $user_id";

                if ($conn->query($sql_update_password) === TRUE) {
                    echo "Password changed successfully.";
                } else {
                    echo "Error updating password: " . $conn->error;
                }
            } else {
                echo "New password and confirm new password do not match.";
            }
        } else {
            echo "Current password is incorrect.";
        }
    } else {
        echo "User not found.";
    }
}

mysqli_close($conn);
?>