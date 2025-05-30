<?php

namespace Sonia\LinguaLair\controllers;

class AdminController {
    private $PermissionsModel;

    public function __construct($PermissionsModel) {
        $this->PermissionsModel = $PermissionsModel;
    }

    public function eliminarUsuario($userIdToDelete) {
        $currentUserId = $_SESSION['user_id'];
        if ($this->PermissionsModel->hasPermission($currentUserId, 'delete_user')) {
            // El administrador tiene permiso para eliminar usuarios
            if ($this->PermissionsModel->deleteUserAndRelatedData($userIdToDelete)) {
                // Éxito al eliminar el usuario y sus datos
                $_SESSION['mensaje'] = "Usuario eliminado con éxito.";
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                // Error al eliminar el usuario o sus datos
                $_SESSION['mensaje'] = "Error al eliminar el usuario de id: " . $userIdToDelete;
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } else {
            // No tiene permiso
            $_SESSION['mensaje'] = "No tienes permiso para eliminar usuarios.";
            $_SESSION['tipo_mensaje'] = 'warning';
        }
    }
    
    public function banearUsuario($userIdToBan) {
        $currentUserId = $_SESSION['user_id'];
        if ($this->PermissionsModel->hasPermission($currentUserId, 'ban_user')) {
            // El administrador tiene permiso para banear usuarios
            if ($this->PermissionsModel->banUser($userIdToBan)) {
                $_SESSION['mensaje'] = "Usuario baneado con éxito.";
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al banear el usuario.";
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } else {
            $_SESSION['mensaje'] = "No tienes permiso para banear usuarios.";
            $_SESSION['tipo_mensaje'] = 'warning';
        }
        header('Location: /admin/usuarios');
        exit();
    }
}

?>