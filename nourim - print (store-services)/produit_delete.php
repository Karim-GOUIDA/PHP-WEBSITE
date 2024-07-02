<?php
session_start();



// Check if the user is logged in and is an admin
if (!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: index.php");
    exit;
}

include ('Connect.php'); 

// Check if the product ID is set in the POST request
if (isset($_POST['produit_id'])) {
    $product_id = $_POST["produit_id"];

    // Using prepared statements to prevent SQL injection
    $sql = "DELETE FROM produits WHERE product_id = ?";
    $stmt = $con->prepare($sql);

    if ($stmt->execute([$product_id])) {
        // Redirect after successful deletion
        header("Location: produits.php");
        exit;
    } else {
        // Error handling if deletion fails
        echo "Error deleting product: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Error deleting product: Product ID not provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Produit</title>
</head>
<body>


</body>
</html>
