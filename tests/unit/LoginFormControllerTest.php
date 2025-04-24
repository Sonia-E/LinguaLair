<?php

namespace Tests\Unit;

use App\Controllers\LoginFormController;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class LoginFormControllerTest extends TestCase
{
    private $modeloMock;
    private $controller;

    protected function setUp(): void
    {
        // Crear un mock del modelo para aislar el controlador
        $this->modeloMock = $this->createMock(\App\Models\UserModel::class);
        $this->controller = new LoginFormController($this->modeloMock);

        // Limpiar cualquier error que pudiera haber quedado de pruebas anteriores
        $this->controller->errores = [];
        $_SESSION = []; // Limpiar la sesión para cada prueba
    }

    // Métodos de prueba irán aquí
}
?>