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
  $keyword_name = $_POST['keyword'];
  
  // Check if category already exists
  $stmt = $conn->prepare("SELECT * FROM keywords WHERE keyword = ?");
  $stmt->bind_param("s", $keyword_name);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo "This keyword already exists.";
  } else {
    // Prepare an insert statement
    $stmt = $conn->prepare("INSERT INTO keywords (keyword) VALUES (?)");
    $stmt->bind_param("s", $keyword_name);

    // Execute the insert statement
    if ($stmt->execute()) {
      echo "New keyword successfully added";
    } else {
      echo "Error adding keyword: " . $stmt->error;
    }
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

<h1>Add a Keyword</h1>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  Category Name: <input type="text" name="keyword"><br>
  <input type="submit" value="Add a keyword">
</form>

</body>
</html>