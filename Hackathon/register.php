<?php
// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password for security
$phonenumber = $_POST['phone-number'];

// Define the path to the JSON file
$jsonFile = 'users.json';

// Check if the JSON file exists, if not create it
if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, json_encode([]));
}

// Read the existing data from the JSON file
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

// Add the new user data
$newUser = [
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'phonenumber' => $phonenumber
];
$data[] = $newUser;

// Save the updated data back to the JSON file
file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

//create a wallet for the user using MasChain API
$apiUrl = 'https://service-testnet.maschain.com/api/wallet/create-user';
$clientId = '99f55f11db1771b614b8650d9069efceda923e04472114d4ba63d7ba7996307c';
$clientSecret = 'sk_ebf43e9b8dca38c04b566f808f1492bcb4ff59fcaf3a36251890ed844293cc23';

$payload = json_encode([
    'name' => $name,
    'email' => $email,
    'phone' => $phonenumber
]);

$options = [
    'http' => [
        'header'  => [
            "Content-type: application/json",
            "client_id: $clientId",
            "client_secret: $clientSecret"
        ],
        'method'  => 'POST',
        'content' => $payload,
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($apiUrl, false, $context);

if ($result === FALSE) {
    echo 'Error creating wallet: ';
    echo 'HTTP code: ' . $http_response_header[0];
    exit();
}

// Decode the result to handle the response
$response = json_decode($result, true);

if (isset($response['status']) && $response['status'] == 200) {
    echo "<p>User registered and wallet created successfully. Redirecting to login page...</p>";
    echo '<meta http-equiv="refresh" content="3;url=login.html">';
} else {
    $errorMsg = isset($response['message']) ? $response['message'] : 'Unknown error';
    echo "<p>User registered but failed to create wallet: $errorMsg. Redirecting to registration page...</p>";
    echo '<meta http-equiv="refresh" content="3;url=register.html">';
}
?>
