<?php
session_start();
session_destroy();
echo 'You have been logged out.';
echo "<form action = 'login.php'>
<button>back to login now</button><br><br>
</form>";
?>

