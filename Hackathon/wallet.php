<?php
// Start the session
session_start();

// Check if the wallet address is set in the session
if (!isset($_SESSION['wallet_address'])) {
    die("No wallet address found. Please log in first.");
}

// Get the wallet address from the session
$walletAddress = $_SESSION['wallet_address'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Address</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #333333;
        }
        p {
            color: #555555;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            color: #333333;
            text-align: left;
        }
        input[type="text"],
        input[type="file"] {
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            width: 100%;
        }
        input[type="submit"] {
            padding: 10px;
            color: #ffffff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome!</h2>
        <p>Your wallet address is: <?php echo htmlspecialchars($walletAddress); ?></p>

        <form action="process.php" method="POST" enctype="multipart/form-data">
            <label for="field1">Name:</label>
            <input type="text" id="field1" name="field1" required>

            <label for="wallet_id">Wallet ID:</label>
            <input type="text" id="wallet_id" name="wallet_id" value="<?php echo htmlspecialchars($walletAddress); ?>" readonly>

            <label for="pdfFile">Supporting Documents (Bank Statement PDF):</label>
            <input type="file" id="pdfFile" name="pdfFile" accept=".pdf" required>

            <input type="submit" value="Submit">
        </form>

        <a href="login.html">Log out</a>
    </div>
</body>
</html>
