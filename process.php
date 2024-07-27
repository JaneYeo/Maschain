<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $field1 = $_POST['field1'];
    $field2 = $_POST['field2'];

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
    move_uploaded_file($pdfFileTmpName, $destinationPath);

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
} else {
    echo "Invalid request method.";
}
?>