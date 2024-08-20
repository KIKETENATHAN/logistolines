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

// Fetch data
$usersResult = $conn->query("SELECT * FROM users");
$customersResult = $conn->query("SELECT * FROM customers");
$transactionsResult = $conn->query("SELECT * FROM transactions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal</title>
   <!-- Tailwind CSS -->
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Your styles here */
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
    .btn {
        padding: 5px 10px;
        color: white;
        background-color: #4CAF50;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-edit {
        background-color: #008CBA;
    }
    .btn-delete {
        background-color: #f44336;
    }
    .btn-print {
        background-color: #FFA500;
    }
    body {
        background-color: gainsboro
    }
</style>
</head>
<body>

<h2>Logistolines Portal</h2>
<a href="logouta.php" style="color:brown"> <b style="float:right">LOG OUT</b></a>
<h3>Users</h3>
<table>
    <thead>
        <tr>
            <th style="color: #008CBA">ID</th>
            <th style="color: #008CBA">Name</th>
            <th style="color: #008CBA">Email</th>
            <!--<th>Phone</th>-->
            <th style="color: #008CBA">reg_date</th>
            <th style="color: #008CBA">store_name</th>
            <th style="color: #008CBA">location_county</th>
            <th style="color: #008CBA">location_street</th>
            <th style="color: #008CBA">location_building</th>
            <th style="color: #008CBA">capacity</th>
            <th style="color: #008CBA">current_capacity</th>
            <th style="color: #008CBA">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $usersResult->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['email'] ?></td>
            <!--<td><?= $row['phone'] ?></td>-->
            <td><?= $row['reg_date'] ?></td>
            <td><?= $row['store_name'] ?></td>
            <td><?= $row['location_county'] ?></td>
            <td><?= $row['location_street'] ?></td>
            <td><?= $row['location_building'] ?></td>
            <td><?= $row['capacity'] ?></td>
            <td><?= $row['current_capacity'] ?></td>
            <td>
                <button class="btn btn-edit" onclick="editUser(<?= $row['id'] ?>)">Edit</button>
                <button class="btn btn-delete" onclick="deleteUser(<?= $row['id'] ?>)">Delete</button>
                <button class="btn btn-print" onclick="printUser(<?= $row['id'] ?>)">Print</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h3>Customers</h3>
<table>
    <thead>
        <tr>
            <th style="color: #008CBA">ID</th>
            <th style="color: #008CBA">User ID</th>
            <th style="color: #008CBA">Customer Name</th>
            <th style="color: #008CBA">Phone Number</th>
            <th style="color: #008CBA">Customer ID</th>
            <th style="color: #008CBA">Email</th>
            <th style="color: #008CBA">Item Description</th>
            <th style="color: #008CBA">Storage Duration</th>
            <th style="color: #008CBA">Item Photo</th>
            <th style="color: #008CBA">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $customersResult->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['customer_name'] ?></td>
            <td><?= $row['phone_number'] ?></td>
            <td><?= $row['customer_id'] ?></td>
            <td><?= $row['customer_email'] ?></td>
            <td><?= $row['item_description'] ?></td>
            <td><?= $row['storage_duration'] ?></td>
            <td><img src="<?= $row['item_photo'] ?>" alt="Item Photo" width="50"></td>
            <td>
                <button class="btn btn-edit" onclick="editCustomer(<?= $row['id'] ?>)">Edit</button>
                <button class="btn btn-delete" onclick="deleteCustomer(<?= $row['id'] ?>)">Delete</button>
                <button class="btn btn-print" onclick="printCustomer(<?= $row['id'] ?>)">Print</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h3>Transactions</h3>
<table>
    <thead>
        <tr>
            <th style="color: #008CBA">ID</th>
            <th style="color: #008CBA">User ID</th>
            <th style="color: #008CBA">Customer ID</th>
            <th style="color: #008CBA">Amount</th>
            <th style="color: #008CBA">Date</th>
            <th style="color: #008CBA">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $transactionsResult->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['customer_id'] ?></td>
            <td><?= $row['amount'] ?></td>
            <td><?= $row['date'] ?></td>
            <td>
                <button class="btn btn-edit" onclick="editTransaction(<?= $row['id'] ?>)">Edit</button>
                <button class="btn btn-delete" onclick="deleteTransaction(<?= $row['id'] ?>)">Delete</button>
                <button class="btn btn-print" onclick="printReceipt(<?= $row['id'] ?>)">Print Receipt</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<script>

    function editUser(id) {
        // Implement edit user functionality
        alert('Edit User: ' + id);
    }

    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = 'delete_user.php?id=' + id;
        }
    }

    function printUSer(id) {
        // Implement print receipt functionality
        alert('Print User: ' + id);
    }

    function editCustomer(id) {
        // Implement edit customer functionality
        alert('Edit Customer: ' + id);
    }

    function deleteCustomer(id) {
        if (confirm('Are you sure you want to delete this customer?')) {
            window.location.href = 'delete_customer.php?id=' + id;
        }
    }
    
    function printCustomer(id) {
        // Implement print receipt functionality
        alert('Print Customer: ' + id);
    }

    function editTransaction(id) {
        // Implement edit transaction functionality
        alert('Edit Transaction: ' + id);
    }

    function deleteTransaction(id) {
        if (confirm('Are you sure you want to delete this transaction?')) {
            window.location.href = 'delete_transaction.php?id=' + id;
        }
    }

    function printReceipt(id) {
        // Implement print receipt functionality
        alert('Print Receipt: ' + id);
    }
</script>

</body>
</html>
<?php
$conn->close();
?>
