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
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];

    // Update the user's first name and last name in the database
    $sql_update_info = "UPDATE user SET first_name = '$first_name', last_name = '$last_name' WHERE user_id = $user_id";

    if ($conn->query($sql_update_info) === TRUE) {
        echo "Personal information updated successfully.";
    } else {
        echo "Error updating personal information: " . $conn->error;
    }
}

mysqli_close($conn);
?>