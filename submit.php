
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die('Invalid CSRF token');
    }
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $userImage = $_FILES['userImage'];
    // Validate uploaded image
    if (!validateImage($userImage)) {
        die('Invalid image file');
    }
    // Check image size
    if ($userImage['size'] > 2 * 1024 * 1024) {
        die('Image file is too large');
    }
    // Move the uploaded image
    $uploadDir = 'uploads/';
    $uploadPath = $uploadDir . basename($userImage['name']);
    if (!move_uploaded_file($userImage['tmp_name'], $uploadPath)) {
        die('Failed to move uploaded image');
    }
    // Save the form data to the database
    saveFormDataToDatabase($firstName, $lastName, $uploadPath);
    unset($_SESSION['csrf_token']);
    $response = ['message' => 'Form submitted successfully'];
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Function to validate CSRF token
function validateCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] == $token;
}

// Function to validate the uploaded image
function validateImage($image)
{
    $validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    return in_array($image['type'], $validImageTypes);
}

function saveFormDataToDatabase($firstName, $lastName, $userImage)
{
    // Replace with your actual database credentials
    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '123456';
    $dbName = 'test_task';

    // Create a connection to the database
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check for connection errors
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Prepare and execute the SQL query to insert the form data
    $stmt = $conn->prepare('INSERT INTO users (first_name, last_name, image_path) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $firstName, $lastName, $userImage);
    $stmt->execute();

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
