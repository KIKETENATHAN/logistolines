<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "logistics";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM customers WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);

    if ($stmt->execute()) {
        header("Location: profile.php?msg=Customer deleted successfully");
        exit;
    } else {
        echo "Error deleting customer: " . $conn->error;
    }
} else {
    echo "No customer ID specified.";
}

$conn->close();
?>
