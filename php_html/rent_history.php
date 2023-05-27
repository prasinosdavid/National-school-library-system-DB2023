<?php
session_start();

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION["user_id"];

// Retrieve user's borrowings
$sql_borrowings = "SELECT br.rent_id, br.book_id, b.ISBN, b.book_title, br.rent_date, br.returned_at
                   FROM book_rent br
                   INNER JOIN book b ON br.book_id = b.book_id
                   WHERE br.user_id = $user_id";

$result_borrowings = $conn->query($sql_borrowings);

// Retrieve user's reservations
$sql_reservations = "SELECT br.reservation_id, br.book_id, b.ISBN, b.book_title, br.reservation_date, br.fulfilled_at
                     FROM book_reservation br
                     INNER JOIN book b ON br.book_id = b.book_id
                     WHERE br.user_id = $user_id";

$result_reservations = $conn->query($sql_reservations);

echo "<h2>Borrowings:</h2>";
if ($result_borrowings->num_rows > 0) {
  echo "<table><tr><th>ISBN</th><th>Title</th><th>Rent Date</th><th>Returned At</th><th>Action</th></tr>";
  // Output data of each borrowing
  while ($row = $result_borrowings->fetch_assoc()) {
    echo "<tr><td>".$row["ISBN"]."</td><td>".$row["book_title"]."</td><td>".$row["rent_date"]."</td><td>".$row["returned_at"]."</td><td><a href='review.php?rent_id=".$row["rent_id"]."'>Review</a></td></tr>";
  }
  echo "</table>";
} else {
  echo "<p>No borrowings.</p>";
}

echo "<h2>Reservations:</h2>";
if ($result_reservations->num_rows > 0) {
  echo "<table><tr><th>ISBN</th><th>Title</th><th>Reservation Date</th><th>Fulfilled At</th><th>Action</th></tr>";
  // Output data of each reservation
  while ($row = $result_reservations->fetch_assoc()) {
    echo "<tr><td>".$row["ISBN"]."</td><td>".$row["book_title"]."</td><td>".$row["reservation_date"]."</td><td>".$row["fulfilled_at"]."</td><td><a href='cancel_reservation.php?reservation_id=".$row["reservation_id"]."'>Cancel</a></td></tr>";
  }
  echo "</table>";
} else {
  echo "<p>No reservations.</p>";
}

mysqli_close($conn);
?>