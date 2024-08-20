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
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $customer_id = $_POST['customer_id'];
    $customer_email = $_POST['customer_email'];
    $item_description = $_POST['item_description'];
    $storage_duration = $_POST['storage_duration'];

    // Handle file upload for item photo
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["item_photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["item_photo"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo json_encode(["error" => "File is not an image."]);
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo json_encode(["error" => "Sorry, file already exists."]);
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["item_photo"]["size"] > 5000000) {
        echo json_encode(["error" => "Sorry, your file is too large."]);
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo json_encode(["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode(["error" => "Sorry, your file was not uploaded."]);
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($_FILES["item_photo"]["tmp_name"], $target_file)) {
            $item_photo = $target_file;
            
            // Insert customer entry into database
            $sql = "INSERT INTO customers (user_id, customer_name, phone_number, customer_id, customer_email, item_description, storage_duration, item_photo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssss", $user_id, $customer_name, $phone_number, $customer_id, $customer_email, $item_description, $storage_duration, $item_photo);

            if ($stmt->execute() === TRUE) {
                // Redirect to profile page
                header("Location: profile.php");
                exit; // Ensure that no further code execution happens after redirection
            } else {
                echo json_encode(["error" => "Error: " . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["error" => "Sorry, there was an error uploading your file."]);
        }
    }
}

$conn->close();
?>
