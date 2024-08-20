<?php
$data = json_decode(file_get_contents('php://input'), true);

if ($data['Body']['stkCallback']['ResultCode'] == 0) {
    // Payment was successful
    $transactionData = $data['Body']['stkCallback']['CallbackMetadata']['Item'];
    
    // Extract relevant information
    $amount = $transactionData[0]['Value'];
    $mpesaReceiptNumber = $transactionData[1]['Value'];
    $transactionDate = $transactionData[3]['Value'];
    $phoneNumber = $transactionData[4]['Value'];
    
    // Save transaction to database
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

    $sql = "INSERT INTO transactions (amount, mpesa_receipt_number, transaction_date, phone_number) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $amount, $mpesaReceiptNumber, $transactionDate, $phoneNumber);

    if ($stmt->execute()) {
        echo "Transaction recorded successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Payment failed
    echo "Payment failed";
}
?>
