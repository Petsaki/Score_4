<?php
$host='localhost';
$db = 'Score_4';
require_once "config_local.php";

$user=$DB_USER;
$pass=$DB_PASS;

//Σύνδεση τοπικά ή στο users
if(gethostname()=='users.iee.ihu.gr') {
	$mysqli = new mysqli($host, $user, $pass, $db,null,'/home/student/it/2017/it175112/mysql/run/mysql.sock');
} else {
        $mysqli = new mysqli($host, $user, $pass, $db);
}

//Μηνυμα λάθους
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>