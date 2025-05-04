<?php

namespace Tests\Unit;

use Sonia\LinguaLair\Controllers\LogController;
use Sonia\LinguaLair\Models\modelo;
use Sonia\LinguaLair\Models\PermissionsModel;
use PHPUnit\Framework\TestCase;

class LogControllerTest extends TestCase
{
    private $modeloMock;
    private $permissionsModelMock;
    private $logController;

    protected function setUp(): void
    {
        $this->modeloMock = $this->createMock(Modelo::class);
        $this->permissionsModelMock = $this->createMock(PermissionsModel::class); // Si lo necesitas
        $this->logController = new LogController($this->modeloMock, $this->permissionsModelMock);

        // Simular la sesión de un usuario
        $_SESSION = ['user_id' => 1];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    // MÉTODOS DE PRUEBA

    public function testProcesarFormularioSuccess()
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'description' => 'Practiced vocabulary',
            'language' => 'en',
            'type' => 'reading',
            'duration' => 30,
            'date' => '2025-04-25',
        ];
        $_SESSION['user_id'] = 1;

        // Configurar expectativas del modelo
        $this->modeloMock->expects($this->once())
            ->method('addLog')
            ->with(1, 'Practiced vocabulary', 'en', 'reading', 30, '2025-04-25')
            ->willReturn(true);

        $this->modeloMock->expects($this->once())
            ->method('addExperience')
            ->with(1, 30);

        $profileData = new \stdClass();
        $profileData->level = 1;
        $profileData->experience = 50;
        $this->modeloMock->expects($this->once())
            ->method('getProfileData')
            ->with(1)
            ->willReturn($profileData);

        // Act
        ob_start(); // Iniciar el buffer de salida para capturar el echo
        $this->logController->procesarFormulario();
        $output = ob_get_clean(); // Obtener el contenido del buffer y limpiarlo

        // Assert
        $this->assertStringContainsString('application/json', getallheaders()['Content-Type']);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => true, 'nuevaExperiencia' => 50, 'nuevoNivel' => 1]),
            $output
        );
    }
}