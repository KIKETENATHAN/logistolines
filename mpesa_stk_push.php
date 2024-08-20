<?php
$consumerKey = 'your_consumer_key'; // Replace with your consumer key
$consumerSecret = 'your_consumer_secret'; // Replace with your consumer secret
$shortCode = 'your_shortcode'; // Replace with your short code
$lipaNaMpesaOnlinePassKey = 'your_passkey'; // Replace with your passkey

$phoneNumber = $_POST['phone_number'];
$amount = $_POST['amount'];
$customerName = $_POST['customer_name'];
$customerId = $_POST['customer_id'];
$customerEmail = $_POST['customer_email'];
$itemDescription = $_POST['item_description'];
$storageDuration = $_POST['storage_duration'];

function generateAccessToken($consumerKey, $consumerSecret) {
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    $result = json_decode($response);

    return $result->access_token;
}

function stkPushRequest($accessToken, $shortCode, $lipaNaMpesaOnlinePassKey, $amount, $phoneNumber) {
    $timestamp = date('YmdHis');
    $password = base64_encode($shortCode . $lipaNaMpesaOnlinePassKey . $timestamp);

    $curl_post_data = [
        'BusinessShortCode' => $shortCode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phoneNumber,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phoneNumber,
        'CallBackURL' => 'https://your_domain.com/mpesa_callback.php',
        'AccountReference' => '12345678',
        'TransactionDesc' => 'Payment for goods'
    ];

    $data_string = json_encode($curl_post_data);

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    $response = curl_exec($curl);

    return json_decode($response);
}

$accessToken = generateAccessToken($consumerKey, $consumerSecret);
$response = stkPushRequest($accessToken, $shortCode, $lipaNaMpesaOnlinePassKey, $amount, $phoneNumber);

// You can add additional logic to handle the response, e.g., saving to a database

echo json_encode($response);
?>
