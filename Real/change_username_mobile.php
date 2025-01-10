<?php
require_once 'database.php'; // Assuming you have already connected $pdo here

header('Content-Type: application/json'); // Ensure JSON response format

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and retrieve the mobile token
    if (!isset($_POST['mobile_token']) || empty($_POST['mobile_token'])) {
        $response = [
            'status' => 'error',
            'message' => 'Mobile token is required.',
        ];
        echo json_encode($response);
        exit;
    }

    $mobile_token = $_POST['mobile_token'];
    $new_username = $_POST['username'];

    try {
        // Fetch the user_id using the mobile token
        $stmt = $pdo->prepare("SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token");
        $stmt->execute(['mobile_token' => $mobile_token]);
        $user_id_result = $stmt->fetch();

        if (!$user_id_result) {
            $response = [
                'status' => 'error',
                'message' => 'Invalid mobile token.',
            ];
            echo json_encode($response);
            exit;
        }

        $user_id = $user_id_result['user_id'];

        // Fetch the current user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            $response = [
                'status' => 'error',
                'message' => 'User not found.',
            ];
            echo json_encode($response);
            exit;
        }

        // Update username if it's different
        if ($new_username !== $user['name']) {
            $stmt = $pdo->prepare("UPDATE users SET name = :new_name WHERE user_id = :user_id");
            $stmt->execute(['new_name' => $new_username, 'user_id' => $user_id]);

            $response = [
                'status' => 'success',
                'message' => 'Username updated successfully.',
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => 'error',
                'message' => 'No changes were made to the username.',
            ];
            echo json_encode($response);
            exit;
        }
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => 'An unexpected error occurred: ' . $e->getMessage(),
        ];
        echo json_encode($response);
        exit;
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method.',
    ];
    echo json_encode($response);
    exit;
}
?>
