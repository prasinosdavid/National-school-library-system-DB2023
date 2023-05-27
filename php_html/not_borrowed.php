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

// Query to find authors whose books have not been borrowed
$sql = "SELECT a.author_first_name, a.author_last_name
        FROM author a
        WHERE NOT EXISTS (SELECT 1
                          FROM book_author ba
                          JOIN book_rent br ON ba.book_id = br.book_id
                          WHERE a.author_id = ba.author_id)";

// Execute the query
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<body>

<h1>Authors whose books have not been borrowed</h1>

<?php
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Author: " . $row["author_first_name"]. " " . $row["author_last_name"]. "<br>";
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