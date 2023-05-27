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
    if (isset($_POST['school_id'])) {
        $school_id = $_POST['school_id'];
        $_SESSION["school_id"] = $school_id;
    }

    if (isset($_POST['teacher_id'])) {
        $teacher_id = $_POST['teacher_id'];

        // Change teacher role to admin
        $sql = "UPDATE user SET role = 'admin' WHERE user_id = $teacher_id";
        if ($conn->query($sql) === TRUE) {
            echo "The teacher was successfully upgraded to admin.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<h1>Register an Operator</h1>

<form method="post" action="">
  <label for="school_id">Select School:</label>
  <select id="school_id" name="school_id">
  <?php
    $sql = "SELECT * FROM school WHERE school_id NOT IN (SELECT DISTINCT school_id FROM user WHERE role = 'admin')";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<option value='".$row["school_id"]."'>".$row["school_name"]."</option>";
        }
    } else {
        echo "No schools available";
    }
  ?>
  </select>
  <input type="submit" value="Select">
</form>

<?php
if (isset($_SESSION["school_id"])) {
?>
<form method="post" action="">
  <label for="teacher_id">Select Teacher:</label>
  <select id="teacher_id" name="teacher_id">
  <?php
    $sql = "SELECT * FROM user WHERE role = 'teacher' AND school_id = ".$_SESSION["school_id"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<option value='".$row["user_id"]."'>".$row["first_name"]." ".$row["last_name"]."</option>";
        }
    } else {
        echo "No teachers available";
    }
  ?>
  </select>
  <input type="submit" value="Make Admin">
</form>
<?php
}
?>

</body>
</html>

<?php
$conn->close();
?>