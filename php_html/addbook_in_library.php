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

// Retrieve all available books from the book table
$sql_books = "SELECT * FROM book WHERE book_id NOT IN (SELECT book_id FROM book_in_library WHERE school_id = ".$_SESSION["school_id"].")";
$result_books = $conn->query($sql_books);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_book = $_POST["selected_book"];
    $no_of_copies = $_POST["no_of_copies"];

    // Insert the book into the library
    $sql_insert_book = "INSERT INTO book_in_library (book_id, school_id, no_of_copies_in_library, last_update)
                        VALUES ('$selected_book', '".$_SESSION["school_id"]."', '$no_of_copies', NOW())";

    if ($conn->query($sql_insert_book) === TRUE) {
        echo "Book added to the library successfully.";
    } else {
        echo "Error adding book to the library: " . $conn->error;
    }
}

mysqli_close($conn);
?>

<!-- Add Book to Library Form -->
<form method="POST" action="addbook_in_library.php">
  <!-- Select book -->
  <label for="selected_book">Select a book:</label>
  <select name="selected_book">
    <?php
    if ($result_books->num_rows > 0) {
        while ($row_book = $result_books->fetch_assoc()) {
            echo "<option value='" . $row_book["book_id"] . "'>" . $row_book["book_title"] . "</option>";
        }
    }
    ?>
  </select><br>

  <!-- Enter number of copies -->
  <label for="no_of_copies">Number of copies:</label>
  <input type="number" name="no_of_copies" required><br>

  <!-- Submit button -->
  <input type="submit" value="Add Book to Library">
</form>