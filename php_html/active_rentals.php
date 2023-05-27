<?php
session_start();

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

// Get school_id from session
$school_id = $_SESSION["school_id"];

// Prepare a select statement to fetch active rentals of the school
$stmt1 = $conn->prepare(
  "SELECT user.username, book.book_title, book_rent.rent_date 
  FROM book_rent 
  JOIN user ON book_rent.user_id = user.user_id 
  JOIN book ON book_rent.book_id = book.book_id 
  WHERE returned_at IS NULL AND user.school_id = ?"
);
$stmt1->bind_param("i", $school_id);
$stmt1->execute();
$rentals = $stmt1->get_result();

// Prepare a select statement to fetch active reservations of the school
$stmt2 = $conn->prepare(
  "SELECT user.username, book.book_title, book_reservation.reservation_date 
  FROM book_reservation 
  JOIN user ON book_reservation.user_id = user.user_id 
  JOIN book ON book_reservation.book_id = book.book_id 
  WHERE fulfilled_at IS NULL AND user.school_id = ?"
);
$stmt2->bind_param("i", $school_id);
$stmt2->execute();
$reservations = $stmt2->get_result();

?>

<!DOCTYPE html>
<html>

<body>

  <h1>Active Rentals and Reservations in Your School</h1>

  <h2>Rentals</h2>
  <?php
  if ($rentals->num_rows > 0) {
    while ($row = $rentals->fetch_assoc()) {
      echo "Username: " . $row["username"] . " - Book: " . $row["book_title"] . " - Rent Date: " . $row["rent_date"] . "<br>";
    }
  } else {
    echo "No active rentals in your school";
  }
  ?>

  <h2>Reservations</h2>
  <?php
  if ($reservations->num_rows > 0) {
    while ($row = $reservations->fetch_assoc()) {
      echo "Username: " . $row["username"] . " - Book: " . $row["book_title"] . " - Reservation Date: " . $row["reservation_date"] . "<br>";
    }
  } else {
    echo "No active reservations in your school";
  }
  ?>

</body>

</html>


<?php
// Close connection
$conn->close();
?>