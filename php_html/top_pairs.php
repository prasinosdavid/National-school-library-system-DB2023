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

// Query to get the top 3 category pairs that appeared in borrowings
$sql = "SELECT c1.category_name AS category_name1, c2.category_name AS category_name2, COUNT(*) as borrowings
        FROM book_rent br
        JOIN book_category bc1 ON br.book_id = bc1.book_id
        JOIN category c1 ON bc1.category_id = c1.category_id
        JOIN book_category bc2 ON br.book_id = bc2.book_id
        JOIN category c2 ON bc2.category_id = c2.category_id
        WHERE c1.category_id < c2.category_id
        GROUP BY c1.category_id, c2.category_id
        ORDER BY borrowings DESC
        LIMIT 3";

// Execute the query
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<body>

<h1>Top 3 Category Pairs in Borrowings</h1>

<?php
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Categories: " . $row["category_name1"]. " and " . $row["category_name2"]. " - Borrowings: " . $row["borrowings"]. "<br>";
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