<?php
// Start the session
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

if (isset($_POST['selected_book'])) {
    $selectedBook = $_POST['selected_book'];
    $school_id = $_SESSION["school_id"];
    
    $sql = "DELETE FROM book_in_library WHERE book_id = ? AND school_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $selectedBook, $school_id);
    
    if ($stmt->execute()) {
        echo " successfully deleted from the library.";
    } else {
        echo "Error deleting book: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "No book selected.";
}

$conn->close();

// Redirect back to the admin library page
header("Location: adminlibrary.php");
exit;

?>