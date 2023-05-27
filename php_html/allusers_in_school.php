<?php
session_start();

// Connection information
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

// Create connection
$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Get school_id from session
$school_id = $_SESSION["school_id"];

// Delete user if delete button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user_id_to_delete = $_POST['user_id'];

  // Prepare a delete statement
  $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
  $stmt->bind_param("i", $user_id_to_delete);

  // Execute the delete statement
  if ($stmt->execute()) {
    echo "User successfully deleted";
  } else {
    echo "Error deleting user: " . $stmt->error;
  }

  // Close statement
  $stmt->close();
}

// Prepare a select statement to fetch users of the school
$stmt = $conn->prepare("SELECT * FROM user WHERE school_id = ?");
$stmt->bind_param("i", $school_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<body>

  <h1>Users in Your School</h1>

  <?php
  if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
      echo "User ID: " . $row["user_id"] . " - Name: " . $row["first_name"] . " " . $row["last_name"] . " - Role: " . $row["role"] . "<br>";
      echo "<form method='post' action=''><input type='hidden' name='user_id' value='" . $row["user_id"] . "'><input type='submit' value='Delete'></form>";
    }
  } else {
    echo "No users found in your school";
  }
  ?>

  <h3>All delayed returns</h2>
    <form action="delayed.php">
      <button>Click here!</button><br><br>
    </form>

    <h3>User ratings per category</h2>
      <form action="average_rating.php">
        <button>Click here!</button><br><br>
      </form>

      <h3>All active rentals and reservations</h2>
        <form action="all_rentals.php">
          <button>Click here!</button><br><br>
        </form>

</body>

</html>


<?php
// Close connection
$conn->close();
?>