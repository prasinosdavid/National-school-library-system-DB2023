<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "library";

$conn = mysqli_connect($servername, $username_db, $password_db, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}



// SQL query to get the counts of book rents
$sql = "SELECT s.school_name, COUNT(br.rent_id) AS number_of_loans 
FROM school AS s
JOIN user AS u ON s.school_id = u.school_id
JOIN book_rent AS br ON u.user_id = br.user_id
WHERE br.rent_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
GROUP BY s.school_id
HAVING number_of_loans > 20";

$result = $conn->query($sql);

// Fetch the number_of_loans counts into an array
$counts = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $counts[] = $row["number_of_loans"];
    }
}

// Find the duplicated counts
$duplicates = array();
foreach (array_count_values($counts) as $val => $c) {
    if($c > 1) {
        $duplicates[] = $val;
    }
}
$flag=0;
// Fetch the schools with the duplicated counts
foreach ($duplicates as $count) {
    $flag =1;
    $sql = "SELECT s.school_name, s.school_id, COUNT(br.rent_id) AS number_of_loans 
    FROM school AS s
    JOIN user AS u ON s.school_id = u.school_id
    JOIN book_rent AS br ON u.user_id = br.user_id
    WHERE br.rent_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
    GROUP BY s.school_id
    HAVING number_of_loans = $count";

    $result = $conn->query($sql);

    // Print out the schools with the same number of loans
    if ($result->num_rows > 0) {
        echo "<h1> School library operators with the same number of loans ($count) in the past year:</h1>";
        while($row = $result->fetch_assoc()) {
            $school_id= $row["school_id"];

            $sql2 = "SELECT u.first_name, u.last_name 
            FROM user AS u
            INNER JOIN school AS s ON s.school_id=u.school_id
            WHERE u.role='admin' AND u.school_id= $school_id";

            $result2 = $conn->query($sql2);
            $row2 = $result2->fetch_assoc();

            echo "Operator name: ".$row2["first_name"]. " ".$row2["last_name"]." School: ".$row["school_name"].  "<br>";
        }
    }
    
}

if($flag==0){
    echo "No results";
}

$conn->close();
?>