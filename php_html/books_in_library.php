<?php
session_start();
// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from database
$sql = "SELECT book.ISBN, book.book_title, book.publisher, book.number_of_pages, book.summary, book.book_language, book_in_library.no_of_copies_in_library, book_in_library.last_update
FROM book
INNER JOIN book_in_library
ON book.ISBN = book_in_library.ISBN";

$result = $conn->query($sql);

// Display data in table format
if ($result->num_rows > 0) {
  echo "<table><tr><th>ISBN</th><th>Title</th><th>Publisher</th><th>Number of Pages</th><th>Summary</th><th>Language</th><th>Copies in Library</th><th>Last Update</th></tr>";
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>".$row["ISBN"]."</td><td>".$row["book_title"]."</td><td>".$row["publisher"]."</td><td>".$row["number_of_pages"]."</td><td>".$row["summary"]."</td><td>".$row["book_language"]."</td><td>".$row["no_of_copies_in_library"]."</td><td>".$row["last_update"]."</td></tr>";
  }
  echo "</table>";
} else {
  echo "0 results";
}

// Close database connection
$conn->close();
?>