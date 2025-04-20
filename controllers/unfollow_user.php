<?php
// unfollow_user.php

session_start();
require_once '../models/SocialModel.php';
$SocialModel = new SocialModel("localhost", "foc", "foc", 'LinguaLair');

if (isset($_POST['follower_id']) && isset($_POST['followed_id'])) {
    $followerId = intval($_POST['follower_id']);
    $followedId = intval($_POST['followed_id']);

    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $followerId) {
        if ($followerId > 0 && $followedId > 0 && $followerId !== $followedId) {
            if ($SocialModel->unfollowUser($followerId, $followedId)) {
                $response = ['success' => true];
            } else {
                $response = ['success' => false, 'message' => 'Error al dejar de seguir al usuario.'];
            }
        } else {
            $response = ['success' => false, 'message' => 'IDs de usuario inválidos.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'El ID del seguidor no coincide con la sesión actual.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Faltan follower_id o followed_id.'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>