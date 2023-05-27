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

$book_id = $_POST['selected_book'];
$user_id = $_SESSION["user_id"];
$school_id = $_SESSION["school_id"];

$sql = "SELECT b.*, GROUP_CONCAT(DISTINCT a.author_first_name, ' ', a.author_last_name) AS authors, GROUP_CONCAT(DISTINCT k.keyword) AS keywords, GROUP_CONCAT(DISTINCT c.category_name) AS categories, no_of_copies_in_library, last_update
                                FROM book_in_library bl
                                LEFT JOIN book b ON bl.book_id = b.book_id
                                LEFT JOIN book_author ba ON b.book_id = ba.book_id
                                LEFT JOIN author a ON ba.author_id = a.author_id
                                LEFT JOIN book_keywords bk ON b.book_id = bk.book_id
                                LEFT JOIN keywords k ON bk.keyword_id = k.keyword_id
                                LEFT JOIN book_category bc ON b.book_id = bc.book_id
                                LEFT JOIN category c ON bc.category_id = c.category_id
                                WHERE bl.school_id = $school_id AND b.book_id = '$book_id'
                                GROUP BY b.ISBN;";

$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo "<h2>Book Information:</h2>";
echo "<p>Title: " . $row['book_title'] . "</p>";
echo "<p>Authors: " . $row['authors'] . "</p>";
echo "<p>Summary: " . $row['summary'] . "</p>";
echo "<p>Keywords: " . $row['keywords'] . "</p>";
echo "<p>Categories: " . $row['categories'] . "</p>";
echo "<p>Copies in Library: " . $row['no_of_copies_in_library'] . "</p>";

$sql2 = "SELECT review_text, review_date, rating
        FROM review
        INNER JOIN book_rent ON review.rent_id = book_rent.rent_id
        WHERE book_rent.book_id = '$book_id'
        ORDER BY review.review_date DESC";

$result = $conn->query($sql2);

echo "<h2>Reviews:</h2>";
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $review_text = $row['review_text'];
    $review_date = $row['review_date'];
    $rating = $row['rating'];

    echo "<p>Review: " . $review_text . " (Date: " . $review_date . ")</p>";
    echo "<p>Rating: " . $rating . " out of 5</p>";
  }
} else {
  echo "<p>No reviews yet.</p>";
}

echo "<h2>Rent or Reserve:</h2>";
echo "<form action='process_rent.php' method='post'>";
echo "<input type='hidden' name='book_id' value='" . $book_id . "'>";
echo "<input type='submit' value='Rent'>";
echo "</form>";


?>