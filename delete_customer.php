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

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "Admin not logged in"]);
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => "Customer deleted successfully"]);
} else {
    echo json_encode(["error" => "Error: " . $conn->error]);
}

$stmt->close();
$conn->close();

header('Location: admin.php');
?>
