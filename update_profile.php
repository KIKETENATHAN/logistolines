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

// Fetch current user data
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_name = $_POST['store_name'];
    $location_county = $_POST['location_county'];
    $location_street = $_POST['location_street'];
    $location_building = $_POST['location_building'];
    $capacity = $_POST['capacity'];

    $sql = "UPDATE users SET 
                store_name=?, 
                location_county=?, 
                location_street=?, 
                location_building=?, 
                capacity=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $store_name, $location_county, $location_street, $location_building, $capacity, $user_id);

    if ($stmt->execute()) {
        header("Location: profile.php?msg=Profile updated successfully");
        exit;
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Update Profile</h2>
            <form action="update_profile.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700">Store Name</label>
                    <input type="text" name="store_name" value="<?php echo htmlspecialchars($user['store_name']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Location County</label>
                    <input type="text" name="location_county" value="<?php echo htmlspecialchars($user['location_county']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Location Street</label>
                    <input type="text" name="location_street" value="<?php echo htmlspecialchars($user['location_street']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Location Building</label>
                    <input type="text" name="location_building" value="<?php echo htmlspecialchars($user['location_building']); ?>" class="mt-1 block w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Capacity</label>
                    <input type="text" name="capacity" value="<?php echo htmlspecialchars($user['capacity']); ?>" class="mt-1 block w-full">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
