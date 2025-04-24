<?php
    class SocialController {
        private $SocialModel;

        public function __construct($SocialModel) {
            $this->SocialModel = $SocialModel;
        }

        public function follow() {
            if (isset($_POST['follower_id']) && isset($_POST['followed_id'])) {
                $followerId = intval($_POST['follower_id']);
                $followedId = intval($_POST['followed_id']);
            
                // Basic security check: Ensure IDs are positive integers
                if ($followerId > 0 && $followedId > 0) {
                    if ($this->SocialModel->followUser($followerId, $followedId)) {
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
        }

        public function unfollow() {
            if (isset($_POST['follower_id']) && isset($_POST['followed_id'])) {
                $followerId = intval($_POST['follower_id']);
                $followedId = intval($_POST['followed_id']);
            
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $followerId) {
                    if ($followerId > 0 && $followedId > 0 && $followerId !== $followedId) {
                        if ($this->SocialModel->unfollowUser($followerId, $followedId)) {
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
        }
}
?>