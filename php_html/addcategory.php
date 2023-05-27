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
  $category_name = $_POST['category_name'];
  
  // Check if category already exists
  $stmt = $conn->prepare("SELECT * FROM category WHERE category_name = ?");
  $stmt->bind_param("s", $category_name);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo "This category already exists.";
  } else {
    // Prepare an insert statement
    $stmt = $conn->prepare("INSERT INTO category (category_name) VALUES (?)");
    $stmt->bind_param("s", $category_name);

    // Execute the insert statement
    if ($stmt->execute()) {
      echo "New category successfully added";
    } else {
      echo "Error adding category: " . $stmt->error;
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

<h1>Add a Category</h1>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  Category Name: <input type="text" name="category_name"><br>
  <input type="submit" value="Add a category">
</form>

</body>
</html>