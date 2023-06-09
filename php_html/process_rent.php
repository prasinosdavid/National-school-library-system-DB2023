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

$book_id = $_POST['book_id'];
$user_id = $_SESSION["user_id"];
$school_id = $_SESSION["school_id"];

$sql = "SELECT number_of_rentals, number_of_reservations, role FROM user WHERE user_id = $user_id ";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if($row['role'] == 'teacher'){

if($row['number_of_rentals'] < 1 || 'number_of_reservations' < 1){

// start transaction
mysqli_begin_transaction($conn);

try {
  // first check if book is available
  $sql = "SELECT no_of_copies_in_library FROM book_in_library WHERE school_id = $school_id AND book_id = '$book_id'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  if($row['no_of_copies_in_library'] > 0) {

    // deduct a copy from the library
    $sql = "UPDATE book_in_library SET no_of_copies_in_library = no_of_copies_in_library - 1 WHERE school_id = $school_id AND book_id = '$book_id'";
    if (!$conn->query($sql)) {
      throw new Exception($conn->error);
    }

    // add a record to the book_rent table
    $sql = "INSERT INTO book_rent (user_id, book_id, rent_date) VALUES ($user_id, '$book_id', CURDATE())";
    if (!$conn->query($sql)) {
      throw new Exception($conn->error);
    }

    // increment the number of rentals for the user
    $sql = "UPDATE user SET number_of_rentals = number_of_rentals + 1 WHERE user_id = $user_id";
    if (!$conn->query($sql)) {
      throw new Exception($conn->error);
    }

    // if no exceptions have been thrown, commit the transaction
    mysqli_commit($conn);
    echo "Book rented successfully!";

  } else {
    $sql = "INSERT INTO book_reservation (user_id, book_id, reservation_date) VALUES ($user_id, '$book_id', CURDATE())";
        if (!$conn->query($sql)) {
          throw new Exception($conn->error);
        }

        // increment the number of rentals for the user
        $sql = "UPDATE user SET number_of_reservations = number_of_reservations + 1 WHERE user_id = $user_id";
        if (!$conn->query($sql)) {
          throw new Exception($conn->error);
        }
    mysqli_commit($conn);
    echo "Book successfully reserved!";
  }

} catch (Exception $e) {
  // an exception has been thrown, rollback the transaction
  mysqli_rollback($conn);
  echo "Error: " . $e->getMessage();
}

}
else {
echo "You can't rent this book. You would have more than 1 active rental or 1 active reservation this week";
}
}
else if($row['role']=='student'){
if($row['number_of_rentals'] < 2){

// start transaction
mysqli_begin_transaction($conn);

try {
  // first check if book is available
  $sql = "SELECT no_of_copies_in_library FROM book_in_library WHERE school_id = $school_id AND book_id = '$book_id'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  if($row['no_of_copies_in_library'] > 0) {

    // deduct a copy from the library
    $sql = "UPDATE book_in_library SET no_of_copies_in_library = no_of_copies_in_library - 1 WHERE school_id = $school_id AND book_id = '$book_id'";
    if (!$conn->query($sql)) {
      throw new Exception($conn->error);
    }

    // add a record to the book_rent table
    $sql = "INSERT INTO book_rent (user_id, book_id, rent_date) VALUES ($user_id, '$book_id', CURDATE())";
    if (!$conn->query($sql)) {
      throw new Exception($conn->error);
    }

    // increment the number of rentals for the user
    $sql = "UPDATE user SET number_of_rentals = number_of_rentals + 1 WHERE user_id = $user_id";
    if (!$conn->query($sql)) {
      throw new Exception($conn->error);
    }

    // if no exceptions have been thrown, commit the transaction
    mysqli_commit($conn);
    echo "Book rented successfully!";

  } else {
    $sql = "INSERT INTO book_reservation (user_id, book_id, reservation_date) VALUES ($user_id, '$book_id', CURDATE())";
            if (!$conn->query($sql)) {
              throw new Exception($conn->error);
            }

            // increment the number of rentals for the user
            $sql = "UPDATE user SET number_of_reservations = number_of_reservations + 1 WHERE user_id = $user_id";
            if (!$conn->query($sql)) {
              throw new Exception($conn->error);
            }
        mysqli_commit($conn);
        echo "Book successfully reserved!";
  }

} catch (Exception $e) {
  // an exception has been thrown, rollback the transaction
  mysqli_rollback($conn);
  echo "Error: " . $e->getMessage();
}

}
else {
echo "You can't rent this book. You would have more than 2 active rentals or 2 active reservations this week";
}
}
?>