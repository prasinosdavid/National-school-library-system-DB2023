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

$query='SELECT school_name FROM school';

$school = $conn->query($query);




mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
</head>
<body>
	<h1>Welcome to the Library Registration System</h1>
	<h2>Already have an account?</h2>
	<form action = "login.php">
        <button>Click to login now!</button><br><br>
    </form>
    <h3>Please complete the following form of registration</h3>

		<h4>Registration Form</h4>

            <form action="register.php" method="POST">
              <label for="first_name">First name:</label>
              <input type="text" id="first_name" name="first_name" required><br><br>
              <label for="last_name">Last name:</label>
              <input type="text" id="last_name" name="last_name" required><br><br>
              <p>Enter your date of birth:</p>
              <select name="year">
                <option value="">Select year</option>
                <?php for ($i = 1900; $i < date('Y'); $i++) : ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
              <select name="month">
                <option value="">Select month</option>
                <?php for ($i = 1; $i <= 12; $i++) : ?>
                <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
              <select name="day">
                <option value="">Select date</option>
                <?php for ($i = 1; $i <= 31; $i++) : ?>
                <option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
              </select><br><br>
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" required><br><br>
              <label for="email">Email:</label>
              <input type="email" id="email" name="email" required><br><br>
              <label for="password">Password:</label>
              <input type="password" id="password" name="password" required><br><br>

                <p>Select your role:</p>
                    <select name="role_selection" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        </select><br><br>
                <p>Select your school:</p>
                    <select name="school_selection" required>
                        <?php while($row1=mysqli_fetch_array($school)):;?>
                        <option><?php echo $row1[0];?></option>
                        <?php endwhile; ?>
                        </select><br><br>
              <input type="submit" value="Register">
            </form>
	</form>

</body>
</html>





<?php
// Retrieve the username and password from the form
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$year = $_POST['year'];
$month = $_POST['month'];
$day = $_POST['day'];
$date = $year.'-'.$month.'-'.$day;
$school = $_POST['school_selection'];
$role = $_POST['role_selection'];

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

$username_check = "SELECT username FROM user WHERE username ='$username'";
$email_check = "SELECT email FROM user WHERE email ='$email'";

if((mysqli_num_rows($conn->query($username_check)) > 0)){
    echo "Username already in use";
}
else if((mysqli_num_rows($conn->query($email_check)) > 0)){
    echo "Email already in use";
}
else{
    $sql4 = "SELECT school_id FROM school WHERE school_name='$school'";
    $school_result = $conn->query($sql4);
    $row2 = $school_result->fetch_assoc();
    $school_id = $row2['school_id'];

    $sql = "INSERT INTO user (username, email, date_of_birth, first_name, last_name, password, role, school_id)
                VALUES ('$username', '$email', '$date', '$first_name', '$last_name', '$password', '$role', '$school_id')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

mysqli_close($conn);
?>
