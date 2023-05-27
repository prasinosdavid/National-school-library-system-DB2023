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

// Get categories
$sql_categories = "SELECT * FROM category";
$result_categories = $conn->query($sql_categories);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    
    // Query to fetch authors in the category
    $sql_authors = "SELECT DISTINCT a.author_first_name, a.author_last_name
                    FROM author a
                    JOIN book_author ba ON a.author_id = ba.author_id
                    JOIN book_category bc ON ba.book_id = bc.book_id
                    WHERE bc.category_id = $category_id";
    
    // Query to fetch teachers who have borrowed books from the category in the last year
    $sql_teachers = "SELECT DISTINCT u.first_name, u.last_name
                     FROM user u
                     JOIN book_rent br ON u.user_id = br.user_id
                     JOIN book_category bc ON br.book_id = bc.book_id
                     WHERE bc.category_id = $category_id AND u.role = 'teacher' AND YEAR(br.rent_date) = YEAR(CURRENT_DATE - INTERVAL 1 YEAR)";
    
    // Execute the queries
    $result_authors = $conn->query($sql_authors);
    $result_teachers = $conn->query($sql_teachers);

    // Display authors
    echo "<h2>Authors in selected category:</h2>";
    while($row = $result_authors->fetch_assoc()) {
        echo "Author: " . $row["author_first_name"] . " " . $row["author_last_name"]."<br>";
    }
    
    // Display teachers
    echo "<h2>Teachers who borrowed from selected category in the last year:</h2>";
    while($row = $result_teachers->fetch_assoc()) {
        echo "Teacher: " . $row["first_name"] . " " . $row["last_name"]."<br>";
    }
}

?>

<!DOCTYPE html>
<html>
<body>

<h1>Select a book category</h1>

<form method="post" action="">
  <label for="category_id">Select Category:</label>
  <select id="category_id" name="category_id">
  <?php
  while($row = $result_categories->fetch_assoc()) {
    echo '<option value="'.$row['category_id'].'">'.$row['category_name'].'</option>';
  }
  ?>
  </select><br>
  <input type="submit" value="Show Info">
</form>

</body>
</html>

<?php
$conn->close();
?>