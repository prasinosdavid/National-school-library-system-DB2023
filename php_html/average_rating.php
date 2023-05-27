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

$school_id = $_SESSION['school_id'];

// Get categories
$sql = "SELECT * FROM category";
$categories_result = mysqli_query($conn, $sql);
$categories = array();
while($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row;
}

$searchQuery = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search_user']) && !empty($_POST['user_id'])) {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $searchQuery .= " AND u.user_id = '$user_id'";
    }
    if (isset($_POST['search_category']) && !empty($_POST['category_id'])) {
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $searchQuery .= " AND c.category_id = '$category_id'";
    }
}

$results = array();
foreach($categories as $category) {
    $category_id = $category['category_id'];
    $sql = "SELECT u.user_id, u.first_name, u.last_name, c.category_name, AVG(r.rating) as avg_rating
            FROM user u
            LEFT JOIN book_rent br ON u.user_id = br.user_id
            LEFT JOIN review r ON br.rent_id = r.rent_id
            LEFT JOIN book_category bc ON br.book_id = bc.book_id
            LEFT JOIN category c ON bc.category_id = c.category_id
            WHERE u.school_id = $school_id AND c.category_id = $category_id $searchQuery
            GROUP BY u.user_id";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $results[$row['user_id']]['name'] = $row["first_name"]." ".$row["last_name"];
            $results[$row['user_id']][$category['category_name']] = $row["avg_rating"];
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<body>

<h2>Average Ratings</h2>

<form method="post" action="">
    Search by User ID: <input type="text" name="user_id"><br>
    Select Category:
    <select name="category_id">
        <option value="">All</option>
        <?php
        foreach($categories as $category) {
            echo "<option value=".$category['category_id'].">".$category['category_name']."</option>";
        }
        ?>
    </select><br>
    <button type="submit" name="search_user">Search</button>
</form>

<?php
if (!empty($results)) {
    echo "<table><tr><th>User ID</th><th>Name</th>";
    foreach($categories as $category) {
        echo "<th>".$category['category_name']."</th>";
    }
    echo "</tr>";
    foreach($results as $user_id => $row) {
        echo "<tr><td>".$user_id."</td><td>".$row['name']."</td>";
        foreach($categories as $category) {
            echo "<td>".(isset($row[$category['category_name']]) ? $row[$category['category_name']] : 'N/A')."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No results";
}
?>

</body>
</html>