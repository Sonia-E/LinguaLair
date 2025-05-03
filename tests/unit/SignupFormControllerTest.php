<?php

namespace Tests\Unit;

use Sonia\LinguaLair\Controllers\SignupFormController;
use Sonia\LinguaLair\Models\Modelo;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;

class SignupFormControllerTest extends TestCase
{
    private $modeloMock;
    private $controller;

    protected function setUp(): void
    {
        $this->modeloMock = $this->createMock(Modelo::class);
        $this->controller = new SignupFormController($this->modeloMock);
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        if (property_exists(SignupFormController::class, 'errores')) {
            $reflectedProperty = new \ReflectionProperty(SignupFormController::class, 'errores');
            $reflectedProperty->setAccessible(true);
            $reflectedProperty->setValue($this->controller, []);
        }
    }

    // 1. Registro de usuario exitoso:
    public function testProcesarFormularioSuccess()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        $this->modeloMock->expects($this->exactly(2))
            ->method('getUserByUsernameOrEmail')
            ->willReturnCallback(function ($arg) {
                if ($arg === 'testuser') {
                    return null;
                }
                if ($arg === 'test@example.com') {
                    return null;
                }
                return null; // Por defecto, aunque no debería llegar aquí en este test
            });

        // Espera que el mock reciba la contraseña en texto plano
        $this->modeloMock->expects($this->once())
            ->method('addNewUser')
            ->with(
                'testuser',
                'testuser',
                $this->callback(function ($password) {
                    // Verifica que la contraseña sea un hash válido
                    return password_verify('password123', $password);
                }),
                'test@example.com',
                'Spain'
            )
            ->willReturn(true);

        // Act
        $result = $this->controller->procesarFormulario();

        // Assert
        $this->assertTrue($result);
        $this->assertArrayHasKey('username', $_SESSION);
        $this->assertEquals('testuser', $_SESSION['username']);
    }

    // 2. Registro de usuario falla por nombre de usuario vacío:
    public function testProcesarFormularioFailsEmptyUsername()
    {
        // Arrange
        $_POST['username'] = '';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('username', $errores);
        $this->assertEquals("Please enter a username", $errores['username']);
    }

    // 3. Registro de usuario falla por nombre de usuario existente:
    public function testProcesarFormularioFailsExistingUsername()
    {
        // Arrange
        $_POST['username'] = 'existinguser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        $this->modeloMock->expects($this->exactly(2)) // Expect 2 calls
            ->method('getUserByUsernameOrEmail')
            ->willReturnCallback(function ($arg) {
                if ($arg === 'existinguser') {
                    return (object)['id' => 1, 'username' => 'existinguser'];
                }
                if ($arg === 'test@example.com') {
                    return null;
                }
                return null; // Default
            });

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('username', $errores);
        $this->assertEquals("This username is already taken", $errores['username']);
    }

    // 4. Registro de usuario falla por contraseña vacía:
    public function testProcesarFormularioFailsEmptyPassword()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = '';
        $_POST['confirm_password'] = '';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $errores);
        $this->assertEquals("Please enter a password", $errores['password']);
    }

    // 5. Registro de usuario falla por contraseña corta:
    public function testProcesarFormularioFailsShortPassword()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = '123';
        $_POST['confirm_password'] = '123';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $errores);
        $this->assertEquals("Password must be at least 5 characters long", $errores['password']);
    }

    // 6. Registro de usuario falla por email vacío:
    public function testProcesarFormularioFailsEmptyEmail()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = '';
        $_POST['country'] = 'Spain';

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $errores);
        $this->assertEquals("Please enter your email address", $errores['email']);
    }

    // 7. Registro de usuario falla por email inválido:
    public function testProcesarFormularioFailsInvalidEmail()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = 'invalid-email';
        $_POST['country'] = 'Spain';

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $errores);
        $this->assertEquals("Please enter a valid email address", $errores['email']);
    }

    // 8. Registro de usuario falla por email existente:
    public function testProcesarFormularioFailsExistingEmail()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = 'existing@example.com';
        $_POST['country'] = 'Spain';

        $this->modeloMock->expects($this->exactly(2))
            ->method('getUserByUsernameOrEmail')
            ->willReturnCallback(function ($arg) {
                if ($arg === 'testuser') {
                    return null;
                }
                if ($arg === 'existing@example.com') {
                    return (object)['id' => 2, 'email' => 'existing@example.com'];
                }
                return null; // Default
            });

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $errores);
        $this->assertEquals("This email address is already registered", $errores['email']);
    }

    // 9. Registro de usuario falla por contraseñas no coincidentes:
    public function testProcesarFormularioFailsMismatchPasswords()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'differentpassword';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('confirm_password', $errores);
        $this->assertEquals("Passwords do not match", $errores['confirm_password']);
    }

    // 10. Registro de usuario falla por error en la base de datos:
    public function testProcesarFormularioFailsDatabaseError()
    {
        // Arrange
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'password123';
        $_POST['confirm_password'] = 'password123';
        $_POST['email'] = 'test@example.com';
        $_POST['country'] = 'Spain';

        $this->modeloMock->expects($this->exactly(2))
            ->method('getUserByUsernameOrEmail')
            ->willReturnCallback(function ($arg) {
                if ($arg === 'testuser') {
                    return null;
                }
                if ($arg === 'test@example.com') {
                    return null;
                }
                return null; // Default
            });

        $this->modeloMock->expects($this->once())
            ->method('addNewUser')
            ->with(
                'testuser',
                'testuser',
                $this->callback(function ($password) {
                    // Assert that the password is a valid hash
                    return password_verify('password123', $password);
                }),
                'test@example.com',
                'Spain'
            )
            ->willReturn(false);

        // Act
        $result = $this->controller->procesarFormulario();
        $errores = $this->controller->getErrores();

        // Assert
        $this->assertFalse($result);
        $this->assertArrayHasKey('registration', $errores);
        $this->assertEquals("Error during registration. Please try again.", $errores['registration']);
    }
}