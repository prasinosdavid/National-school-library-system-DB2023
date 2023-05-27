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

$sql = "SELECT first_name, last_name, school_id FROM user WHERE user_id= '$user_id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){
$row = $result->fetch_assoc();
$firstname = $row['first_name'];
$lastname = $row['last_name'];
$school_id = $row['school_id'];
}

$_SESSION["school_id"] = $school_id;

$sql2 = "SELECT school_name FROM school WHERE school_id = '$school_id'";
$result = mysqli_query($conn, $sql2);

if(mysqli_num_rows($result) > 0){
$row = $result->fetch_assoc();
$schoolname = $row['school_name'];
}


   ?>

<!DOCTYPE html>
<html>

<head>
	<title>Library Home</title>
</head>

<h1>Welcome to the Library of <?php echo $schoolname;?> </h1>
<h2>
Hello there <?php echo $firstname;?> <?php echo $lastname, $user_id;?> <br><br>
<h3>Search for Books<h3>
<form method="get" action="adminsearch.php">
  <input type="text" name="query" placeholder="Search...">
  <button type="submit">Go</button>
</form><br><br>
Here are all the available books:
</h2>
<body>
<?php

if (isset($_GET['query'])) {
  $query = $_GET['query'];

  // TODO: Implement search logic here

  echo "Search results for: " . htmlspecialchars($query);
}

$sql = "SELECT b.*, GROUP_CONCAT(DISTINCT a.author_first_name, ' ', a.author_last_name) AS authors, GROUP_CONCAT(DISTINCT k.keyword) AS keywords, GROUP_CONCAT(DISTINCT c.category_name) AS categories, no_of_copies_in_library, last_update
                                FROM book_in_library bl
                                LEFT JOIN book b ON bl.book_id = b.book_id
                                LEFT JOIN book_author ba ON b.book_id = ba.book_id
                                LEFT JOIN author a ON ba.author_id = a.author_id
                                LEFT JOIN book_keywords bk ON b.book_id = bk.book_id
                                LEFT JOIN keywords k ON bk.keyword_id = k.keyword_id
                                LEFT JOIN book_category bc ON b.book_id = bc.book_id
                                LEFT JOIN category c ON bc.category_id = c.category_id
                                WHERE bl.school_id = $school_id
                                GROUP BY b.book_id
                                ORDER BY b.book_title;";

$result = $conn->query($sql);

$filename = 'C:\xampp\htdocs\php_html\images\book_image.jpg';
$imageData = file_get_contents($filename);
$image = base64_encode($imageData);
    $src = 'data:image/png;base64,' . $image;

// Display data in table format
if ($result->num_rows > 0) {
   echo "<form method='post' action='deletebook.php'>";
  echo "<table><tr><th>ISBN</th><th>Title</th><th>Image</th><th>Publisher</th><th>Author</th><th>Number of Pages</th><th>Category</th><th>Summary</th><th>Keywords</th><th>Language</th><th>Copies in Library</th><th>Last Update</th><th>Select Book</th></tr>";
  // output data of each row
  while($row = $result->fetch_assoc()) {

    echo "<tr><td>".$row["ISBN"]."</td><td>".$row["book_title"]."</td><td><img src='".$src."' alt='Book cover' width='100'></td><td>".$row["publisher"]."</td><td>".$row["authors"]."</td><td>".$row["number_of_pages"]."</td><td>".$row["categories"]."</td><td>".$row["summary"]."</td><td>".$row["keywords"]."</td><td>".$row["book_language"]."</td><td>".$row["no_of_copies_in_library"]."</td><td>".$row["last_update"]."</td><td><input type='radio' name='selected_book' value='".$row["book_id"]."'></td></tr>";


  }

echo "</table><br></br>";
    echo "<input type='submit' value='Delete selected book'>";
      echo "</form>";



} else {
  echo "0 results";
}




?>
<h2>Want to add a book?</h2>
	<form action = "addbook.php">
        <button>Add book!</button><br><br>
    </form>
<h2>Want to make changes to your account?</h2>
	<form action = "manageaccount.php">
        <button>Click here!</button><br><br>
    </form>
     <h3>Want to see your rental history?</h2>
    <form action = "rent_history.php">
            <button>Click here!</button><br><br>
        </form>
<h3>Want to see all the school users?</h2>
    <form action = "allusers_in_school.php">
            <button>Click here!</button><br><br>
        </form>
</body>
</html>