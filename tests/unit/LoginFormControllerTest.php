<?php

namespace Tests\Unit;

use Sonia\LinguaLair\Controllers\LoginFormController;
use Sonia\LinguaLair\Models\modelo;
use PHPUnit\Framework\TestCase;
class LoginFormControllerTest extends TestCase
{

    private $modeloMock;
    private $controller;

    protected function setUp(): void
    {
        $this->modeloMock = $this->createMock(Modelo::class);
        $this->controller = new LoginFormController($this->modeloMock);
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    // MÉTODOS DE PRUEBA

    // 1. Inicio de Sesión Exitoso:
    public function testCheckLoginSuccess()
    {
        // Arrange
        $loginIdentifier = 'testuser';
        $password = 'password123';
        $usuarioSimulado = new \stdClass();
        $usuarioSimulado->id = 1;
        $usuarioSimulado->username = $loginIdentifier;
        $usuarioSimulado->email = 'test@example.com';
        $usuarioSimulado->password = $password; // En la realidad esto sería el hash

        $this->modeloMock->expects($this->once())
            ->method('getUserByUsernameOrEmail')
            ->with($loginIdentifier)
            ->willReturn($usuarioSimulado);

        // Act
        $usuarioLogueado = $this->controller->check_login($loginIdentifier, $password);

        // Assert
        $this->assertInstanceOf(\stdClass::class, $usuarioLogueado);
        $this->assertEquals(1, $usuarioLogueado->id);
    }

    // 2. Inicio de Sesión Fallido por Usuario Inexistente:
    public function testCheckLoginFailsUserNotFound()
    {
        // Arrange
        $loginIdentifier = 'nonexistentuser';
        $password = 'anypassword';

        $this->modeloMock->expects($this->once())
            ->method('getUserByUsernameOrEmail')
            ->with($loginIdentifier)
            ->willReturn(null);

        // Act
        $usuarioLogueado = $this->controller->check_login($loginIdentifier, $password);

        // Assert
        $this->assertNull($usuarioLogueado);
        $errores = $this->controller->getErrores();
        $this->assertArrayHasKey('username', $errores);
        $this->assertEquals("Incorrect username or email", $errores['username']);
    }

    // 3. Inicio de Sesión Fallido por Contraseña Incorrecta:
    public function testCheckLoginFailsIncorrectPassword()
    {
        // Arrange
        $loginIdentifier = 'testuser';
        $correctPassword = 'password123';
        $incorrectPassword = 'wrongpassword';
        $usuarioSimulado = new \stdClass();
        $usuarioSimulado->id = 1;
        $usuarioSimulado->username = $loginIdentifier;
        $usuarioSimulado->email = 'test@example.com';
        $usuarioSimulado->password = $correctPassword; // Contraseña correcta

        $this->modeloMock->expects($this->once())
            ->method('getUserByUsernameOrEmail')
            ->with($loginIdentifier)
            ->willReturn($usuarioSimulado);

        // Act
        $usuarioLogueado = $this->controller->check_login($loginIdentifier, $incorrectPassword);

        // Assert
        $this->assertNull($usuarioLogueado);
        $errores = $this->controller->getErrores();
        $this->assertArrayHasKey('password', $errores);
        $this->assertEquals("Incorrect password", $errores['password']);
    }

    // 4. Prueba de procesarFormulario()
    public function testProcesarFormularioSuccess()
    {
        error_log("TEST: Inicio de testProcesarFormularioSuccess()");

        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';

        $usuarioSimulado = new \stdClass();
        $usuarioSimulado->id = 1;
        $usuarioSimulado->role_id = 1;
        $usuarioSimulado->username = 'testuser';
        $usuarioSimulado->email = 'test@example.com';
        $usuarioSimulado->password = 'password123';

        $this->modeloMock->expects($this->once())
            ->method('getUserByUsernameOrEmail')
            ->with('testuser')
            ->willReturn($usuarioSimulado);

        error_log("TEST: Modelo mock configurado.");

        // Act
        error_log("TEST: Llamando a \$this->controller->procesarFormulario()");
        $resultado = $this->controller->procesarFormulario();
        error_log("TEST: Retorno de \$this->controller->procesarFormulario(): " . var_export($resultado, true));

        // Assert
        error_log("TEST: Ejecutando aserciones.");
        $this->assertTrue($resultado, 'Se esperaba que procesarFormulario() devolviera true en caso de éxito.');
        $this->assertArrayHasKey('user_id', $_SESSION);
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertArrayHasKey('user_role', $_SESSION);
        $this->assertEquals('standard', $_SESSION['user_role']);
        error_log("TEST: Fin de testProcesarFormularioSuccess()");
    }


    // 5. Dejar campo nombre de usuario vacío
    public function testProcesarFormularioFailsValidationEmptyUsername()
    {
        // Arrange
        $_POST['username'] = ' ';
        $_POST['password'] = 'password123';

        // Act
        $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertArrayHasKey('username', $errores);
        $this->assertEquals("Please enter your username or email", $errores['username']);
        $this->assertArrayNotHasKey('user_id', $_SESSION);
    }
    

    // 6. Dejar campo contraseña vacío
    public function testProcesarFormularioFailsValidationEmptyPassword()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = '';

        // Act
        $this->controller->procesarFormulario();

        // Assert
        $this->assertArrayHasKey('password', $this->controller->errores);
        $this->assertEquals("Please enter your password", $this->controller->errores['password']);
        $this->assertArrayNotHasKey('user_id', $_SESSION);
    }

    // 7. Contraseña incorrecta
    public function testProcesarFormularioFailsLoginIncorrectPassword()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'wrongpassword';

        $usuarioSimulado = new \stdClass();
        $usuarioSimulado->id = 1;
        $usuarioSimulado->role_id = 1;
        $usuarioSimulado->username = 'testuser';
        $usuarioSimulado->email = 'test@example.com';
        $usuarioSimulado->password = 'correctpassword'; // Contraseña correcta en la base de datos

        $this->modeloMock->expects($this->once())
            ->method('getUserByUsernameOrEmail')
            ->with('testuser')
            ->willReturn($usuarioSimulado);

        // Act
        $this->controller->procesarFormulario();

        // Assert
        $this->assertArrayHasKey('password', $this->controller->errores); // Probablemente sea 'password'
        $this->assertEquals("Incorrect password", $this->controller->errores['password']);
        $this->assertArrayNotHasKey('user_id', $_SESSION);
    }

}