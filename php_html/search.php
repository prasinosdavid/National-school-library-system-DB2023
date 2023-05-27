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
$school_id  = $_SESSION["school_id"];

if (isset($_GET['query'])) {
  $query = $_GET['query'];


  $sql = "SELECT b.*, (
            SELECT GROUP_CONCAT(DISTINCT CONCAT(a.author_first_name, ' ', a.author_last_name))
            FROM book_author ba
            LEFT JOIN author a ON ba.author_id = a.author_id
            WHERE ba.book_id = b.book_id
          ) AS authors, (
            SELECT GROUP_CONCAT(DISTINCT c.category_name)
            FROM book_category bc
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE bc.book_id = b.book_id
          ) AS categories, (
            SELECT GROUP_CONCAT(DISTINCT k.keyword)
            FROM book_keywords bk
            LEFT JOIN keywords k ON bk.keyword_id = k.keyword_id
            WHERE bk.book_id = b.book_id
          ) AS keywords, bl.no_of_copies_in_library, bl.last_update
          FROM book_in_library bl
          LEFT JOIN book b ON bl.book_id = b.book_id
          WHERE bl.school_id = $school_id AND bl.no_of_copies_in_library > 0
          AND ( b.book_title LIKE '$query%' OR EXISTS (
            SELECT *
            FROM book_author ba
            LEFT JOIN author a ON ba.author_id = a.author_id
            WHERE ba.book_id = b.book_id AND (a.author_first_name LIKE '$query%' OR a.author_last_name LIKE '$query%')
          ) OR EXISTS (
            SELECT *
            FROM book_category bc
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE bc.book_id = b.book_id AND c.category_name LIKE '$query%'
          ))";

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      echo "<form method='post' action='rent_book.php'>";
      echo "<table><tr><th>ISBN</th><th>Title</th><th>Image</th><th>Publisher</th><th>Author</th><th>Number of Pages</th><th>Category</th><th>Summary</th><th>Keywords</th><th>Language</th><th>Copies in Library</th><th>Last Update</th><th>Select Book</th></tr>";
      // output data of each row
      while ($row = $result->fetch_assoc()) {
          $image = base64_encode(file_get_contents('C:\xampp\htdocs\php_html\images\book_image.jpg'));
          $src = 'data:image/png;base64,' . $image;

          echo "<tr><td>".$row["ISBN"]."</td><td>".$row["book_title"]."</td><td><img src='".$src."' alt='Book cover' width='100'></td><td>".$row["publisher"]."</td><td>".$row["authors"]."</td><td>".$row["number_of_pages"]."</td><td>".$row["categories"]."</td><td>".$row["summary"]."</td><td>".$row["keywords"]."</td><td>".$row["book_language"]."</td><td>".$row["no_of_copies_in_library"]."</td><td>".$row["last_update"]."</td><td><input type='radio' name='selected_book' value='".$row["ISBN"]."'></td></tr>";
      }

      echo "</table><br></br>";
      echo "<input type='submit' value='Rent selected book'>";
      echo "</form>";
  } else {
      echo "0 results";
  }
  // TODO: Implement search logic here

  echo "Search results for: " . htmlspecialchars($query);
}
?>