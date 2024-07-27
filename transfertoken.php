<?php
//Activate wallet by id
$walletid = isset($_POST['wallet_id']) ? htmlspecialchars($_POST['wallet_id']) : '';
if (empty($walletid)) {
    die('Wallet ID is required.');
}

$url = "https://service-testnet.maschain.com/api/wallet/wallet/6111/activate";
$clientId = '1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241';
$clientSecret = 'sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b';



$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n" .
                     "client_id: $clientId\r\n" .
                     "client_secret: $clientSecret\r\n",
        'method'  => 'POST'
        
    ]
];


$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    die('Error creating wallet');
}

$resultData = json_decode($result, true);

if (isset($resultData['status']) && $resultData['status'] === 200 && isset($resultData['result']['wallet']['wallet_address'])) {
    $userWalletAddress = $resultData['result']['wallet']['wallet_address'];
    
    // Call transferTokens function here if needed
    $walletAddress = $userWalletAddress;
    console.log($walletAddress);
$amount = 1;
$contractAddress = "0xa7e30c1c27BB46932Fc1466FF472e134d689B4D6";
$callbackUrl = "https://postman-echo.com/post";

$dataInput = [
    'wallet_address' => $walletAddress,
    'to' => 0xebD0a58Ea912C39d251E8C215cfc9af7c29d6228, // Note: $recipientAddress is not defined in the original code
    'amount' => $amount,
    'contract_address' => $contractAddress,
    'callback_url' => $callbackUrl
];
$key = "1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241";
$secret = "sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b";

$apiUrl = 'https://service-testnet.maschain.com/api/token/token-transfer';

try {
    $ch = curl_init($apiUrl);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataInput));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'client_id: ' . $key,
        'client_secret: ' . $secret,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    
    curl_close($ch);
    $data = json_decode($response, true);
    echo 'Token transfer result: ' . json_encode($data, JSON_PRETTY_PRINT);
} catch (Exception $error) {
    echo 'Error transferring tokens: ' . $error->getMessage();
}
} else {
    die('Wallet creation failed or unexpected response structure');
}


   
 ?>


