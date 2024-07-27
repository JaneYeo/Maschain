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

    // Add the new user data to users.json
    $newUser = [
        'name' => $name,
        'ic' => $ic,
        'password' => password_hash($password, PASSWORD_DEFAULT), // Ensure password is hashed
        'phone_number' => $phoneNumber,
        'email' => $email
    ];
    $users[] = $newUser;

    // Save the updated data back to the users.json file
    writeJsonFile('users.json', $users);

    // Notify user of successful registration and redirect
    echo '<script>
    alert("Registration successful! You will be redirected to the login page.");
    setTimeout(function() {
        window.location.href = "login.html";
    }, 3000); // 3000 milliseconds = 3 seconds
    </script>';
    exit();
}
?>
