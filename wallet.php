<?php

// Execute the JavaScript file and capture its output
$output = shell_exec('node createwallet.js 2>&1');

// Parse the JSON output
$start = strpos($output, '{');
$end = strrpos($output, '}');
if ($start !== false && $end !== false) {
    $jsonOutput = substr($output, $start, $end - $start + 1);
    $data = json_decode($jsonOutput, true);
} else {
    die("Failed to parse JSON from output");
}

// Extract the relevant information
if (isset($data['result']['user']) && isset($data['result']['wallet'])) {
    $user = $data['result']['user'];
    $wallet = $data['result']['wallet'];

    $newWallet = [
        'name' => $user['name'],
        'email' => $user['email'],
        'ic' => $user['ic'],
        'phonenumber' => $user['phone'],
        'wallet_id' => $wallet['wallet_id'],
        'wallet_address' => $wallet['wallet_address']
    ];
} else {
    die("Required data not found in the output");
}

// Define the path to the JSON file
$jsonFile = 'wallet.json';

// Check if the JSON file exists, if not create it
if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, json_encode([]));
}

// Read the existing data from the JSON file
$jsonData = file_get_contents($jsonFile);
$existingData = json_decode($jsonData, true);

// Add the new wallet data
$existingData[] = $newWallet;

// Save the updated data back to the JSON file
file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));

echo "New wallet data has been added to wallet.json\n";