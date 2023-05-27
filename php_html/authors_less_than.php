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

// Query to get all authors who have written at least 5 books less than the author with the most books
$sql = "SELECT a.author_first_name, a.author_last_name, COUNT(ba.book_id) as num_books
        FROM author a
        JOIN book_author ba ON a.author_id = ba.author_id
        GROUP BY a.author_id
        HAVING num_books <= (SELECT MAX(num_books) - 5 FROM 
        (SELECT COUNT(ba.book_id) as num_books
        FROM author a
        JOIN book_author ba ON a.author_id = ba.author_id
        GROUP BY a.author_id) subquery)
	ORDER BY num_books DESC";

// Execute the query
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<body>

<h1>Authors who have written at least 5 books less than the author with the most books</h1>

<?php
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Author: " . $row["author_first_name"]. " " . $row["author_last_name"]. " - Books: " . $row["num_books"]. "<br>";
    }
} else {
    echo "No results";
}
?>

</body>
</html>

<?php
$conn->close();
?>