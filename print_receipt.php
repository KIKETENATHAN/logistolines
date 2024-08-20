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

// Fetch transaction data
$sql = "SELECT * FROM transactions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    echo json_encode(["error" => "Transaction not found"]);
    exit;
}

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $transaction['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

// Fetch customer data
$sql = "SELECT * FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $transaction['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo json_encode(["error" => "Customer not found"]);
    exit;
}

// Print receipt
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .receipt {
        max-width: 600px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .receipt-details {
        margin-bottom: 20px;
    }
    .receipt-details th, .receipt-details td {
        padding: 10px;
        text-align: left;
    }
</style>
</head>
<body>

<div class="receipt">
    <div class="receipt-header">
        <h2>Receipt</h2>
        <p>Date: <?= $transaction['date'] ?></p>
    </div>
    <div class="receipt-details">
        <table>
            <tr>
                <th>User Name:</th>
                <td><?= $user['name'] ?></td>
            </tr>
            <tr>
                <th>User Email:</th>
                <td><?= $user['email'] ?></td>
            </tr>
            <tr>
                <th>Customer Name:</th>
                <td><?= $customer['customer_name'] ?></td>
            </tr>
            <tr>
                <th>Amount:</th>
                <td><?= $transaction['amount'] ?></td>
            </tr>
        </table>
    </div>
</div>

<script>
    window.print();
</script>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
