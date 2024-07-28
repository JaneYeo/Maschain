<?php
// Function to validate the IC number format (Assuming Malaysian IC format: 6 digits - 2 digits - 4 digits)
function isValidIC($ic) {
    return preg_match('/^\d{6}-\d{2}-\d{4}$/', $ic);
}

// Function to read data from a JSON file
function readJsonFile($filePath) {
    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode([]));
    }
    $jsonData = file_get_contents($filePath);
    return json_decode($jsonData, true);
}

// Function to write data to a JSON file
function writeJsonFile($filePath, $data) {
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

// Function to check if the IC number exists and meets the criteria in the database.json
function validateIc($ic, $database) {
    foreach ($database as $entry) {
        if (isset($entry['ic'], $entry['race'], $entry['category'], $entry['alive']) &&
            $entry['ic'] === $ic &&
            strtolower($entry['race']) === 'malay' &&
            strtolower($entry['category']) === 'b40' &&
            $entry['alive'] === true) {
            return true;
        }
    }
    return false;
}

// Function to check if the IC number already exists in users.json
function icExists($ic, $data) {
    foreach ($data as $user) {
        if (isset($user['ic']) && $user['ic'] === $ic) {
            return true;
        }
    }
    return false;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $ic = $_POST['ic'] ?? '';
    $password = $_POST['password'] ?? '';
    $phoneNumber = $_POST['phone-number'] ?? '';


    // Validate the IC number format
    if(!isValidIC($ic)) {
        die('Registration failed: Invalid IC number format. Please use the format 123456-78-1234.');
    }

    // Read the existing data from the database.json file
    $database = readJsonFile('database.json');

    // Validate the IC number against the database
    if (!validateIc($ic, $database)) {
        die('Registration failed: User does not meet the criteria or is not found in the database.');
    }

    // Read the existing data from the users.json file
    $users = readJsonFile('users.json');

    // Check if the IC number already exists in users.json
    if (icExists($ic, $users)) {
        die('Registration failed: User with this IC number already exists.');
    }


    $url = 'https://service-testnet.maschain.com/api/wallet/create-user';
$clientId = '1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241';
$clientSecret = 'sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b';

$payload = json_encode([
    'name' => $name,
    'email' => $email,
    'ic' => $ic,
    'phone' => $phoneNumber
]);

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n" .
                     "client_id: $clientId\r\n" .
                     "client_secret: $clientSecret\r\n",
        'method'  => 'POST',
        'content' => $payload
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    die('Error creating wallet');
}

$resultData = json_decode($result, true);

if (isset($resultData['status']) && $resultData['status'] === 200 && isset($resultData['result']['wallet']['wallet_address'])) {
    $recipientAddress = $resultData['result']['wallet']['wallet_address'];
    $recipientWalletId = $resultData['result']['wallet']['wallet_id'];
    echo "Recipient ID: " . $recipientAddress;
    // Call transferTokens function here if needed
    $walletAddress = "0xebD0a58Ea912C39d251E8C215cfc9af7c29d6228";
$amount = 1;
$contractAddress = "0xa7e30c1c27BB46932Fc1466FF472e134d689B4D6";
$callbackUrl = "https://postman-echo.com/post";

$dataInput = [
    'wallet_address' => $walletAddress,
    'to' => $recipientAddress, // Note: $recipientAddress is not defined in the original code
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


// Add the new user data to users.json
$newUser = [
    'name' => $name,
    'ic' => $ic,
    'password' => password_hash($password, PASSWORD_DEFAULT), // Ensure password is hashed
    'phone_number' => $phoneNumber,
    'email' => $email,
    'walled_address' => $recipientAddress
];
$users[] = $newUser;

// Save the updated data back to the users.json file
writeJsonFile('users.json', $users);

    // Notify user of successful registration and redirect
    echo '<script>
    alert("Registration successful! You will be redirected to the login page.");
    setTimeout(function() {
        window.location.href = "login.html";
    }, 3000); // 3000 milliseconds = 10 seconds
    </script>';
    exit();
}

?>

