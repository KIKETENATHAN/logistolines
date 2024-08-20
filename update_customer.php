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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Fetch customer data
    $sql = "SELECT * FROM customers WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found.";
        exit;
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['id'];
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $customer_email = $_POST['customer_email'];
    $item_description = $_POST['item_description'];
    $storage_duration = $_POST['storage_duration'];

    // Update customer data
    $sql = "UPDATE customers SET customer_name=?, phone_number=?, customer_email=?, item_description=?, storage_duration=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $customer_name, $phone_number, $customer_email, $item_description, $storage_duration, $customer_id);

    if ($stmt->execute()) {
        header("Location: profile.php?msg=Customer updated successfully");
        exit;
    } else {
        echo "Error updating customer: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function printCustomerDetails() {
            var printContents = document.getElementById("customerDetails").innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-lg p-6" id="customerDetails">
            <h2 class="text-2xl font-semibold mb-4">Edit Customer</h2>
            <form action="update_customer.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($customer['id']); ?>">
                <div class="mb-4">
                    <label class="block text-yellowS-700">Customer Name</label>
                    <input type="text" name="customer_name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Phone Number</label>
                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($customer['phone_number']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Customer Email</label>
                    <input type="email" name="customer_email" value="<?php echo htmlspecialchars($customer['customer_email']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Item Description</label>
                    <textarea name="item_description" class="mt-1 block w-full"><?php echo htmlspecialchars($customer['item_description']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Storage Duration</label>
                    <input type="text" name="storage_duration" value="<?php echo htmlspecialchars($customer['storage_duration']); ?>" class="mt-1 block w-full">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Customer</button>
                <button type="button" class="bg-green-500 text-white px-4 py-2 rounded" onclick="printCustomerDetails()">Print Customer Details</button>
            </form>
        </div>
    </div>
</body>
</html>
