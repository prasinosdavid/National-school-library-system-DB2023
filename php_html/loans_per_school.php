<?php
// Connection information
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

// Create connection
$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $year = $_POST['year'];
    $month = $_POST['month'];

    $sql = "SELECT s.school_name, IFNULL(COUNT(br.rent_id),0) as total_loans
            FROM school s
            LEFT JOIN user u ON s.school_id = u.school_id
            LEFT JOIN book_rent br ON u.user_id = br.user_id AND YEAR(br.rent_date) = $year AND MONTH(br.rent_date) = $month
            GROUP BY s.school_id
            ORDER BY total_loans DESC";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        echo "School: " . $row["school_name"] . " - Total loans in selected month: " . $row["total_loans"]."<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<h1>Total number of loans per school</h1>

<form method="post" action="">
  <label for="year">Select Year:</label>
  <input type="number" id="year" name="year" min="2000" max="2023" step="1" value="2023" /><br>
  
  <label for="month">Select Month:</label>
  <select id="month" name="month">
    <option value="1">January</option>
    <option value="2">February</option>
    <option value="3">March</option>
    <option value="4">April</option>
    <option value="5">May</option>
    <option value="6">June</option>
    <option value="7">July</option>
    <option value="8">August</option>
    <option value="9">September</option>
    <option value="10">October</option>
    <option value="11">November</option>
    <option value="12">December</option>
  </select><br>
  
  <input type="submit" value="Calculate">
</form>

</body>
</html>

<?php
$conn->close();
?>