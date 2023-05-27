<?php
session_start();

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$school_id = $_SESSION["school_id"];
$user_id = $_SESSION["user_id"];

$sql = "SELECT br.rent_id, u.first_name, u.last_name, b.book_title, br.rent_date, br.returned_at
        FROM book_rent br
        INNER JOIN user u ON br.user_id = u.user_id
        INNER JOIN book b ON br.book_id = b.book_id
        WHERE u.school_id = $school_id AND returned_at IS NULL";

$result = mysqli_query($conn, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['return'])) {
        $rent_id = $_POST['rent_id'];

        $update_rent = "UPDATE book_rent SET returned_at = CURDATE() WHERE rent_id = $rent_id";
        $update_library = "UPDATE book_in_library SET no_of_copies_in_library = no_of_copies_in_library + 1 WHERE school_id = $school_id AND book_id IN (SELECT book_id FROM book_rent WHERE rent_id = $rent_id)";
        $update_user = "UPDATE user SET number_of_rentals = number_of_rentals - 1 WHERE user_id = $user_id";

        if (mysqli_query($conn, $update_rent) && mysqli_query($conn, $update_library) && mysqli_query($conn, $update_user)) {
            echo "Book returned successfully!";
        } else {
            echo "Error updating records: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html>
<body>

<h2>All Rentals</h2>

<?php
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "First Name: " . $row["first_name"]. " - Last Name: " . $row["last_name"]. " - Book Title: " . $row["book_title"]. " - Rent Date: " . $row["rent_date"]. " - Returned At: " . $row["returned_at"]. " <form method='post' action=''><button type='submit' name='return'>Return</button><input type='hidden' name='rent_id' value='".$row["rent_id"]."'></form><br>";
    }
} else {
    echo "No results";
}
?>

</body>
</html>

<?php
mysqli_close($conn);
?>