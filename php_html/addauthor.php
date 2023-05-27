<?php
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];

  // Prepare an insert statement
  $stmt = $conn->prepare("INSERT INTO author (author_first_name, author_last_name) VALUES (?, ?)");
  $stmt->bind_param("ss", $firstname, $lastname);

  // Execute the insert statement
  if ($stmt->execute()) {
    echo "New author successfully added";
  } else {
    echo "Error adding author: " . $stmt->error;
  }

  // Close statement
  $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<body>

<h1>Add an Author</h1>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  First name: <input type="text" name="firstname"><br>
  Last name: <input type="text" name="lastname"><br>
  <input type="submit" value="Add an author">
</form>

</body>
</html>