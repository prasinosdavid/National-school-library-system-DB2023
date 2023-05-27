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

// Retrieve authors, categories, and keywords from the database
$sql_authors = "SELECT * FROM author";
$result_authors = $conn->query($sql_authors);

$sql_categories = "SELECT * FROM category";
$result_categories = $conn->query($sql_categories);

$sql_keywords = "SELECT * FROM keywords";
$result_keywords = $conn->query($sql_keywords);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ISBN = $_POST["ISBN"];
    $book_title = $_POST["book_title"];
    $publisher = $_POST["publisher"];
    $number_of_pages = $_POST["number_of_pages"];
    $summary = $_POST["summary"];
    $book_language = $_POST["book_language"];
    $selected_authors = $_POST["authors"];
    $selected_categories = $_POST["categories"];
    $selected_keywords = $_POST["keywords"];

    // Check if the book already exists in the database
    $sql_check_book = "SELECT * FROM book WHERE ISBN = '$ISBN'";
    $result_check_book = $conn->query($sql_check_book);



    if ($result_check_book->num_rows > 0) {
        echo "Book with the given ISBN already exists in the database.";
    } else {
        // Insert the book into the database
        $sql_insert_book = "INSERT INTO book (ISBN, book_title, publisher, number_of_pages, summary, book_language)
                            VALUES ('$ISBN', '$book_title', '$publisher', '$number_of_pages', '$summary', '$book_language')";

        if ($conn->query($sql_insert_book) === TRUE) {
            $book_id = $conn->insert_id;

            // Insert the book's authors into the database
            foreach ($selected_authors as $author_id) {
                $sql_insert_book_author = "INSERT INTO book_author (author_id, book_id) VALUES ('$author_id', '$book_id')";
                $conn->query($sql_insert_book_author);
            }

            // Insert the book's categories into the database
            foreach ($selected_categories as $category_id) {
                $sql_insert_book_category = "INSERT INTO book_category (category_id, book_id) VALUES ('$category_id', '$book_id')";
                $conn->query($sql_insert_book_category);
            }

            // Insert the book's keywords into the database
            foreach ($selected_keywords as $keyword_id) {
                $sql_insert_book_keyword = "INSERT INTO book_keywords (keyword_id, book_id) VALUES ('$keyword_id', '$book_id')";
                $conn->query($sql_insert_book_keyword);
            }



            echo "Book added successfully.";
        } else {
            echo "Error adding book: " . $conn->error;
        }
    }
}

mysqli_close($conn);
?>

<!-- Add Book Form -->
<form method="POST" action="addbook.php">
  <!-- Book details -->
  <label for="ISBN">ISBN:</label>
  <input type="text" name="ISBN" required><br>
  <label for="book_title">Title:</label>
  <input type="text" name="book_title" required><br>
  <label for="publisher">Publisher:</label>
  <input type="text" name="publisher" required><br>
  <label for="number_of_pages">Number of Pages:</label>
  <input type="number" name="number_of_pages" required><br>
  <label for="summary">Summary:</label>
  <textarea name="summary" required></textarea><br>
  <label for="book_language">Language:</label>
  <input type="text" name="book_language" required><br>

  <!-- Select authors -->
  <label for="authors">Authors:</label><br>

    <?php
    if ($result_authors->num_rows > 0) {
        while ($row_author = $result_authors->fetch_assoc()) {
            echo "<input type='checkbox' name='authors[]' value='" . $row_author["author_id"] . "'>" . $row_author["author_first_name"] ." ". $row_author["author_last_name"]. "<br>";
        }
    }
    ?>


  <!-- Select categories -->
  <label for="categories">Categories:</label><br>

    <?php
    if ($result_categories->num_rows > 0) {
        while ($row_category = $result_categories->fetch_assoc()) {
            echo "<input type='checkbox' name='categories[]' value='" . $row_category["category_id"] . "'>" . $row_category["category_name"] . "<br>";
        }
    }
    ?>


  <!-- Select keywords -->
  <label for="keywords">Keywords:</label><br>
  <?php
  if ($result_keywords->num_rows > 0) {
      while ($row_keyword = $result_keywords->fetch_assoc()) {
          echo "<input type='checkbox' name='keywords[]' value='" . $row_keyword["keyword_id"] . "'>" . $row_keyword["keyword"] . "<br>";
      }
  }
  ?>

  <!-- Submit button -->
  <input type="submit" value="Add Book">



</form>

<h2>Want to add an already registered book to your school library?</h2>
  	<form action = "addbook_in_library.php">
          <button>Add book in your library!</button><br><br>
      </form>

<h2>Want to register a new author?</h2>
  	<form action = "addauthor.php">
          <button>Register a new author!</button><br><br>
      </form>

      <h2>Want to register a new category?</h2>
        	<form action = "addcategory.php">
                <button>Register a new category!</button><br><br>
            </form>
<h2>Want to register a new keyword?</h2>
        	<form action = "addkeyword.php">
                <button>Register a new keyword!</button><br><br>
            </form>