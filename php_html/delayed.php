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

$sql = "SELECT u.first_name, u.last_name, br.rent_date, br.returned_at, b.book_title,
               (CASE
                    WHEN br.returned_at IS NULL AND br.rent_date < CURDATE() THEN DATEDIFF(CURDATE(), br.rent_date)
                    ELSE 0
                END) AS delay_days
        FROM user u
        LEFT JOIN book_rent br ON u.user_id = br.user_id
        LEFT JOIN book b ON br.book_id = b.book_id
        WHERE u.school_id = $school_id AND br.returned_at IS NULL AND br.rent_date < CURDATE()
        ORDER BY delay_days DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>
<body>

<h2>All Users</h2>

<?php
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "First Name: " . $row["first_name"]. " - Last Name: " . $row["last_name"]. " - Delayed Book: " . $row["book_title"] . " - Delay Days: " . $row["delay_days"]. "<br>";
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