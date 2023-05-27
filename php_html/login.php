<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Library Home</title>
</head>
<body>
	<h1>Welcome to the Library</h1>
	<h2>Don't have an account?</h2>
	<form action = "register.php">
        <button>Create an account now</button><br><br>
    </form>
    <h3>Please login to access the library</h3>
	<form method="post" action="login.php">
		<label for="username">Username:</label>
		<input type="text" id="username" name="username"><br><br>
		<label for="password">Password:</label>
		<input type="password" id="password" name="password"><br><br>
		<input type="submit" value="Login">
	</form>

</body>
</html>



<?php
session_start();

// Retrieve the username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

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


// Prepare the SQL statement
$sql = "SELECT user_id, role, school_id FROM user WHERE username='$username' AND Password='$password'";
$result = mysqli_query($conn, $sql);




    if(mysqli_num_rows($result) > 0){
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $role = $row['role'];
    $school_id = $row['school_id'];

    $_SESSION["user_id"] = $user_id;
    $_SESSION["school_id"] = $school_id;

    // Redirect the user based on their role
    if ($role == 'admin') {

      header("Location: adminlibrary.php");
    }
    else if ($role == 'teacher') {

      header("Location: library.php");
    }
    else if ($role == 'student') {

          header("Location: library.php");
        }
    else if ($role == 'universal'){
        header("Location: library_uni.php");
    }
}
else {
  // User was not found, display an error message
  echo "Invalid username or password";
}

mysqli_close($conn);
?>