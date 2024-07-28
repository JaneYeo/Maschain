<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $field1 = $_POST['field1'];
    $field2 = $_POST['wallet_id'];

    // Handle file upload
    $pdfFile = $_FILES['pdfFile'];
    $pdfFileName = $pdfFile['name'];
    $pdfFileTmpName = $pdfFile['tmp_name'];

    // Check if it's a PDF file
    $fileExtension = strtolower(pathinfo($pdfFileName, PATHINFO_EXTENSION));
    if ($fileExtension != "pdf") {
        die("Error: Only PDF files are allowed.");
    }

    // Move the uploaded file to a desired location
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $destinationPath = $uploadDir . uniqid() . '_' . $pdfFileName;
    if (!move_uploaded_file($pdfFileTmpName, $destinationPath)) {
        die("Error: File upload failed.");
    }

    // Create new data entry
    $newData = [
        'field1' => $field1,
        'field2' => $field2,
        'pdfFile' => $destinationPath
    ];

    // Read existing JSON data
    $jsonFile = 'data.json';
    $jsonData = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

    // Append new data
    $jsonData[] = $newData;

    // Save updated JSON data
    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));

    echo "New data has been successfully added to the JSON file.";

    // Token transfer details
    $destination = "0xebD0a58Ea912C39d251E8C215cfc9af7c29d6228";
    $source = $field2; // Use the wallet_id from form data
    $amount = 1;
    $contractAddress = "0xa7e30c1c27BB46932Fc1466FF472e134d689B4D6";
    $callbackUrl = "https://postman-echo.com/post";

    $data = [
        'wallet_address' => $source,
        'to' => $destination,
        'amount' => $amount,
        'contract_address' => $contractAddress,
        'callback_url' => $callbackUrl
    ];

    $key = "1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241";
    $secret = "sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b";

    // API endpoint
    $apiUrl = 'https://service-testnet.maschain.com/api/token/token-transfer';

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'client_id: ' . $key,
        'client_secret: ' . $secret,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        die('cURL Error: ' . curl_error($ch));
    }

    // Close cURL session
    curl_close($ch);

    // Decode and display the response
    $responseData = json_decode($response, true);
    echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";

    //Second smart contract
    $destination = $field2;
    $source = "0xebD0a58Ea912C39d251E8C215cfc9af7c29d6228"; // Use the wallet_id from form data
    $amount = 200;
    $contractAddress = "0x7B8e1169d11eEc48e67Ed0B705E4Db22A8ED01ef";
    $callbackUrl = "https://postman-echo.com/post";

    $data = [
        'wallet_address' => $source,
        'to' => $destination,
        'amount' => $amount,
        'contract_address' => $contractAddress,
        'callback_url' => $callbackUrl
    ];

    $key = "1a716398e73c7e2055cdab5aee60fdf86eee3f2a99c8141952eb7c8274b6f241";
    $secret = "sk_7d02e3f5281a2aed13f005fa6c37b3c5ff3bff86be179c500f8e9e1dddebc43b";

    // API endpoint
    $apiUrl = 'https://service-testnet.maschain.com/api/token/token-transfer';

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'client_id: ' . $key,
        'client_secret: ' . $secret,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        die('cURL Error: ' . curl_error($ch));
    }

    // Close cURL session
    curl_close($ch);

    // Decode and display the response
    $responseData = json_decode($response, true);
    echo "<pre>" . json_encode($responseData, JSON_PRETTY_PRINT) . "</pre>";

    // Display success message
    echo "Token transfer successful. <a href='wallet.php'>Go back</a>";
} else {
    echo "Invalid request method.";
}
?>
