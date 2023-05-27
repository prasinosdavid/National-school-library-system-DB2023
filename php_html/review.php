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

// Check if the rent_id is provided in the URL
if (isset($_GET['rent_id'])) {
    $rent_id = $_GET['rent_id'];

    // Retrieve the book details for the given rent_id
    $sql_book = "SELECT br.book_id, b.book_title, b.ISBN
                 FROM book_rent br
                 INNER JOIN book b ON br.book_id = b.book_id
                 WHERE br.rent_id = $rent_id";

    $result_book = $conn->query($sql_book);

    if ($result_book->num_rows > 0) {
        $row_book = $result_book->fetch_assoc();
        $ISBN = $row_book["ISBN"];
        $book_title = $row_book["book_title"];
    } else {
        echo "Book not found.";
        exit;
    }
} else {
    echo "Rent ID not provided.";
    exit;
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_text = $_POST["review_text"];
    $rating = $_POST["rating"];

    // Insert the review into the database
    $sql_insert_review = "INSERT INTO review (rent_id, review_text, review_date, rating)
                          VALUES ( $rent_id, '$review_text', NOW(), $rating)";

    if ($conn->query($sql_insert_review) === TRUE) {
        echo "Review submitted successfully.";
    } else {
        echo "Error inserting review: " . $conn->error;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave a Review</title>
</head>
<body>
    <h2>Leave a Review for "<?php echo $book_title; ?>"</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?rent_id=" . $rent_id; ?>">
        <label for="review_text">Review:</label><br>
        <textarea id="review_text" name="review_text" rows="4" cols="50"></textarea><br>

        <label for="rating">Rating (1-5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required><br>

        <input type="submit" value="Submit Review">
    </form>
</body>
</html>