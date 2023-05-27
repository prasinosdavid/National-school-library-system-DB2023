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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $school_name = $_POST['school_name'];
  $school_address = $_POST['school_address'];
  $town = $_POST['town'];
  $postal_code = $_POST['postal_code'];
  $email = $_POST['email'];
  $telephone = $_POST['telephone'];
  $principal_firstname = $_POST['principal_firstname'];
  $principal_lastname = $_POST['principal_lastname'];

 
  $sql = "INSERT INTO school (school_name, school_address, town, postal_code, email, telephone, school_principal_firstname, school_principal_lastname) 
    VALUES ('$school_name', '$school_address', '$town', '$postal_code', '$email', '$telephone', '$principal_firstname', '$principal_lastname')";

  
  if ($conn->query($sql) === TRUE) {
    echo "School successfully registered";
  } else {
    echo "Error registering school: " . $conn->error;
  }
}

?>

<!DOCTYPE html>
<html>

<body>

  <h1>Register a New School</h1>

  <form method="post" action="">
    School Name: <input type="text" name="school_name"><br>
    School Address: <input type="text" name="school_address"><br>
    Town: <input type="text" name="town"><br>
    Postal Code: <input type="text" name="postal_code"><br>
    Email: <input type="email" name="email"><br>
    Telephone: <input type="text" name="telephone"><br>
    Principal's First Name: <input type="text" name="principal_firstname"><br>
    Principal's Last Name: <input type="text" name="principal_lastname"><br>
    <input type="submit" value="Register School">
  </form>

</body>

</html>

<?php
// Close connection
$conn->close();
?>