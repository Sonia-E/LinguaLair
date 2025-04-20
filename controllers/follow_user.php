<?php
session_start();
require_once '../models/SocialModel.php';
$SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');

if (isset($_POST['follower_id']) && isset($_POST['followed_id'])) {
    $followerId = intval($_POST['follower_id']);
    $followedId = intval($_POST['followed_id']);

    // Basic security check: Ensure IDs are positive integers
    if ($followerId > 0 && $followedId > 0) {
        if ($SocialModel->followUser($followerId, $followedId)) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Failed to follow user.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Invalid user IDs.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Missing follower_id or followed_id.'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>