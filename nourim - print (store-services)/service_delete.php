<?php
session_start();

// Check if the user is logged in and is an admin

if (!isset($_SESSION["login"]) && !isset($_SESSION["admin"]) && $_SESSION["admin"] !== true) {
    
    header("Location: loginpage.php");
    exit; 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php

echo $_POST["service_id"] ;

if (isset($_POST['service_id'])) {
    
    include_once 'connect.php';

    $service_id = $_POST["service_id"] ;

    $sql = "DELETE FROM services WHERE service_id = '$service_id'";

    $req = $con->prepare($sql) ;

    if ($req->execute()) {

        header("Location: services.php");
        exit; // Stop further execution

    } else {
        // Error handling if deletion fails
        echo "Error deleting service" . mysqli_error($con);
    }



}else{
    echo "Error deleting service " ;
}

?>
    
</body>
</html>


<?php



?>

