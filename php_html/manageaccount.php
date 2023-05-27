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

// Retrieve the user's information from the database
$sql_user = "SELECT * FROM user WHERE user_id = $user_id";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    $row_user = $result_user->fetch_assoc();

    // Display common account information
    echo "<h2>Account Information:</h2>";
    echo "Username: " . $row_user["username"] . "<br>";
    echo "Email: " . $row_user["email"] . "<br>";

    // Check if the user is a student or a teacher
    if ($row_user["role"] == "student") {
        // Display student-specific information
        echo "Role: Student<br>";
        echo "First Name: " . $row_user["first_name"] . "<br>";
        echo "Last Name: " . $row_user["last_name"] . "<br>";

        echo "<h2>Change Password:</h2>";
        echo "<form method='POST' action='changepassword.php'>";
        echo "Current Password: <input type='password' name='current_password' required><br>";
        echo "New Password: <input type='password' name='new_password' required><br>";
        echo "Confirm New Password: <input type='password' name='confirm_new_password' required><br>";
        echo "<input type='submit' value='Change Password'>";
        echo "</form>";
    } elseif ($row_user["role"] == "teacher" || $row_user["role"] == "admin") {
        // Display teacher-specific information and change information form
        echo "Role: Teacher<br>";
        echo "First Name: " . $row_user["first_name"] . "<br>";
        echo "Last Name: " . $row_user["last_name"] . "<br>";

        echo "<h2>Change Personal Information:</h2>";
        echo "<form method='POST' action='updateinfo.php'>";
        echo "First Name: <input type='text' name='first_name' value='" . $row_user["first_name"] . "'><br>";
        echo "Last Name: <input type='text' name='last_name' value='" . $row_user["last_name"] . "'><br>";
        echo "Email: <input type='text' name='email' value='" . $row_user["email"] . "'><br>";
        echo "<input type='submit' value='Update Information'>";
        echo "</form>";

        echo "<h2>Change Password:</h2>";
        echo "<form method='POST' action='changepassword.php'>";
        echo "Current Password: <input type='password' name='current_password' required><br>";
        echo "New Password: <input type='password' name='new_password' required><br>";
        echo "Confirm New Password: <input type='password' name='confirm_new_password' required><br>";
        echo "<input type='submit' value='Change Password'>";
        echo "</form>";
    }
} else {
    echo "User not found.";
}

echo "Want to log out?";
echo "<form action = 'logout.php'>
<button>Log out now</button><br><br>
</form>";

mysqli_close($conn);
?>

