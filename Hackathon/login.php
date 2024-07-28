<?php
// Start the session
session_start();

// Get form data
$name = $_POST['name'];
$password = $_POST['password'];

// Define the path to the JSON file
$jsonFile = 'users.json';

// Check if the JSON file exists
if (!file_exists($jsonFile)) {
    die("User database not found.");
}

// Read the existing data from the JSON file
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

// Check credentials
$userFound = false;
$walletAddress = '';

foreach ($data as $user) {
    if ($user['name'] === $name && password_verify($password, $user['password'])) {
        $userFound = true;
        $walletAddress = $user['walled_address'];
        break;
    }
}

if ($userFound) {
    // Store the wallet address in the session
    $_SESSION['wallet_address'] = $walletAddress;
    // Redirect to the wallet page
    header("Location: wallet.php");
    exit;
} else {
    echo "Invalid email or password. <a href='login.html'>Try again</a>";
}
?>
