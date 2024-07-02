<?php
$srvrname = "localhost";
$dbname = "nourim";
$user = "root";
$pass = "";

try {
    $con = new PDO("mysql:host=$srvrname;dbname=$dbname", $user, $pass);
} catch (PDOException $ex) {
    echo ($ex);
    exit();
}
?>