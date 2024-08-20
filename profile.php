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

$user_id = $_SESSION['user_id'];

// Fetch user profile
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user customers
$customer_sql = "SELECT * FROM customers WHERE user_id=?";
$customer_stmt = $conn->prepare($customer_sql);
$customer_stmt->bind_param("i", $user_id);
$customer_stmt->execute();
$customers = $customer_stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleProfile() {
            var profileSection = document.getElementById("profileSection");
            if (profileSection.style.display === "none") {
                profileSection.style.display = "block";
            } else {
                profileSection.style.display = "none";
            }
        }

        function printCustomers() {
            var printContents = document.getElementById("customerTable").innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
</head>
<body class="bg-yellow-100">
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4 cursor-pointer" onclick="toggleProfile()">
                Welcome, <?php echo htmlspecialchars($user['username']); ?>
            </h2>
            <div id="profileSection" style="display: none;">
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p>Store Name: <?php echo htmlspecialchars($user['store_name']); ?></p>
                <p>Location County: <?php echo htmlspecialchars($user['location_county']); ?></p>
                <p>Location Street: <?php echo htmlspecialchars($user['location_street']); ?></p>
                <p>Location Building: <?php echo htmlspecialchars($user['location_building']); ?></p>
                <p>Capacity: <?php echo htmlspecialchars($user['capacity']); ?></p>
                <button class="bg-blue-500 text-white px-4 py-2 rounded mt-4" onclick="window.location.href='update_profile.php'">Update Profile</button>
                <button class="bg-red-500 text-white px-4 py-2 rounded mt-4" onclick="window.location.href='delete_profile.php'">Delete Profile</button>
            </div>
            <button class="bg-blue-500 text-white px-4 py-2 rounded mt-4" onclick="window.location.href='logout.php'">Logout</button>
            <button class="bg-green-500 text-white px-4 py-2 rounded mt-4" onclick="window.location.href='customer.html'">Add Customer</button>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 mt-8">
            <h2 class="text-2xl font-semibold mb-4">Your Customers</h2>
            <button class="bg-blue-500 text-white px-4 py-2 rounded mb-4" onclick="printCustomers()">Print Customers</button>
            <div id="customerTable">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">Customer Name</th>
                            <th class="py-2">Phone Number</th>
                            <th class="py-2">Customer ID</th>
                            <th class="py-2">Email</th>
                            <th class="py-2">Item Description</th>
                            <th class="py-2">Storage Duration</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2"><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($customer['phone_number']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($customer['customer_id']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($customer['customer_email']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($customer['item_description']); ?></td>
                            <td class="py-2"><?php echo htmlspecialchars($customer['storage_duration']); ?></td>
                            <td class="py-2">
                                <button class="bg-yellow-500 text-white px-2 py-1 rounded" onclick="window.location.href='update_customer.php?id=<?php echo $customer['id']; ?>'">Update</button>
                                <button class="bg-red-500 text-white px-2 py-1 rounded" onclick="window.location.href='delcustomer.php?id=<?php echo $customer['id']; ?>'">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

