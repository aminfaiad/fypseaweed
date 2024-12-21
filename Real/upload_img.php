<?php
require 'database.php'; // Include database connection
//error_reporting(E_ALL);  // Report all errors
//ini_set('display_errors', 1);  // Display errors to the browser
// Set the directory to save the uploaded images
$uploadDir = 'uploads/';

// Ensure the directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if a file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && isset($_POST['farm_token'])) {
    $file = $_FILES['image'];
    $farmToken = $_POST['farm_token'];
    $currentTime = date('Y-m-d H:i:s'); // Current timestamp in MySQL datetime format
    //echo $file['error'];
    //echo 'upload_max_filesize: ' . ini_get('upload_max_filesize');
    //echo 'post_max_size: ' . ini_get('post_max_size');
    //echo "TEST";

    // Validate the file type (optional)
    $imageDetails = getimagesize($file['tmp_name']);

if ($imageDetails === false) {
    http_response_code(400);
    echo json_encode(['error' => 'The file is not a valid image.']);
    exit;
}

// Extract MIME type from the result of getimagesize()
$fileMimeType = $imageDetails['mime'];

// Define allowed image MIME types
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if (!in_array($fileMimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
    exit;
}

// Process the image and convert to PNG
try {
    switch ($fileMimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($file['tmp_name']);
            break;
        default:
            throw new Exception('Unsupported image type.');
    }

        // Temporarily save the converted PNG for hashing
        $tempPath = tempnam(sys_get_temp_dir(), 'img_') . '.png';
        if (!imagepng($image, $tempPath)) {
            throw new Exception('Failed to convert image to PNG.');
        }
        imagedestroy($image); // Free memory

        // Generate the MD5 hash of the converted PNG file
        $fileContent = file_get_contents($tempPath);
        $hash = md5($fileContent); // Generate MD5 hash
        $hashedFileName = $hash . '.png';
        $targetPath = $uploadDir . $hashedFileName;

        // Move the temporary file to the final destination
        if (!rename($tempPath, $targetPath)) {
            unlink($tempPath); // Cleanup if renaming fails
            throw new Exception('Failed to save the converted PNG file.');
        }

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO farm_images (farm_token, `time`, image_path) VALUES (:farm_token, :created_at, :image_path)");
        $stmt->execute([
            ':farm_token' => $farmToken,
            ':created_at' => $currentTime,
            ':image_path' => $targetPath,
        ]);

        // Respond with success
        http_response_code(200);
        echo json_encode([
            'message' => 'File uploaded, converted to PNG, hashed, and data saved successfully.',
            'path' => $targetPath,
            'hash' => $hash,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded, missing farm_token, or invalid request method.']);
}
?>
