<?php
session_start();



// Connect to the database
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>


<!DOCTYPE html>
<html>

<head>
    <title>Library Home</title>
</head>

<body>
    <h1>Welcome to the Library database control panel</h1>
    <h2>Register a school</h2>
    <form action='register_school.php'>
        <button>Register a new school now</button><br><br>
    </form>
    <h2>Register a school operator</h2>
    <form action='register_operator.php'>
        <button>Register a new school operator now</button><br><br>
    </form>
    <h2>Number of Loans per school</h2>
    <form action='loans_per_school.php'>
        <button>Number of Loans per school</button><br><br>
    </form>
    <h2>Category authors and teachers in the last year</h2>
    <form action='category_info.php'>
        <button>Category authors and teachers in the last year</button><br><br>
    </form>
    <h2>Young teachers with most borrowings</h2>
    <form action='young_teachers.php'>
        <button>Young teachers with most borrowings</button><br><br>
    </form>
    <h2>Authors whose books have not been borrowed</h2>
    <form action='not_borrowed.php'>
        <button>Authors whose books have not been borrowed</button><br><br>
    </form>
    <h2>Top 3 category pairs in borrowings</h2>
    <form action='top_pairs.php'>
        <button>Top 3 category pairs in borrowings</button><br><br>
    </form>
    <h2>All authors who have written at least 5 books less than the author with the most books</h2>
    <form action='authors_less_than.php'>
        <button>All authors who have written at least 5 books less than the author with the most books</button><br><br>
    </form>
    <h2>All school operators with the same amount of lents, to have lented at least 20 books in the past 1 year</h2>
    <form action='equal_operators.php'>
        <button>All authors who have written at least 5 books less than the author with the most books</button><br><br>
    </form>

<form method="post" action="backup_database.php">
    <input type="submit" value="Backup Database">
</form>
<form method="post" action="restore_database.php">
    <input type="text" name="filename" placeholder="Enter backup filename">
    <input type="submit" value="Restore Database">
</form>

</body>

</html>