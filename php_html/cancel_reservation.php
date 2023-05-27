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

// Check if the reservation_id is provided in the URL
if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];

    // Retrieve the reservation details for the given reservation_id
    $sql_reservation = "SELECT *
                        FROM book_reservation
                        WHERE reservation_id = $reservation_id";

    $result_reservation = $conn->query($sql_reservation);

    if ($result_reservation->num_rows > 0) {
        $row_reservation = $result_reservation->fetch_assoc();

        // Check if the reservation belongs to the logged-in user
        if ($row_reservation['user_id'] == $user_id) {
            $book_id = $row_reservation['book_id'];

            // Delete the reservation from the database
            $sql_delete_reservation = "DELETE FROM book_reservation
                                       WHERE reservation_id = $reservation_id";

            if ($conn->query($sql_delete_reservation) === TRUE) {
                echo "Reservation canceled successfully.";
            } else {
                echo "Error canceling reservation: " . $conn->error;
            }
        } else {
            echo "You are not authorized to cancel this reservation.";
        }
    } else {
        echo "Reservation not found.";
        exit;
    }
} else {
    echo "Reservation ID not provided.";
    exit;
}

mysqli_close($conn);
?>