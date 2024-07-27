<?php
// Get form data
$email = $_POST['email'];
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
foreach ($data as $user) {
    if ($user['email'] === $email && password_verify($password, $user['password'])) {
        $userFound = true;
        break;
    }
}

if ($userFound) {
    echo "Login successful. <a href='login.html'>Go back</a>";
} else {
    echo "Invalid email or password. <a href='login.html'>Try again</a>";
}
?>
