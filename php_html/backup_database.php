<?php
session_start();
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost";
	    $username_db = "root";
        $password_db = "";
        $dbname = "library";
        $backup_file = 'C:/' . $dbname . date("Y-m-d-H-i-s") . '.sql';

        $command = "mysqldump --user={$username_db} --password={$password_db} --host={$servername} {$dbname} > {$backup_file}";

        system($command, $output);

        if($output == 0) {
            echo "Database backup successful.";
        } else {
            echo "Database backup failed.";
        }
    }
?>
