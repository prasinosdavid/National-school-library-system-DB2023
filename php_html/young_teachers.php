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

// Query to find young teachers who have borrowed the most books and the number of books they have borrowed
$sql = "SELECT u.first_name, u.last_name, COUNT(br.book_id) as num_borrowed
        FROM user u
        JOIN book_rent br ON u.user_id = br.user_id
        WHERE u.role = 'teacher' AND TIMESTAMPDIFF(YEAR, u.date_of_birth, CURDATE()) < 40
        GROUP BY u.user_id
        ORDER BY num_borrowed DESC";

// Execute the query
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<body>

<h1>Young Teachers who have borrowed the most books</h1>

<?php
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Teacher: " . $row["first_name"]. " " . $row["last_name"]. " - Number of books borrowed: " . $row["num_borrowed"]. "<br>";
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