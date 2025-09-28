<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_POST) {
    include 'config/database.php';
    
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Este correo electrónico ya está registrado';
        } else {
            // Crear usuario
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, activo) VALUES (?, ?, ?, 1)");
            
            if ($stmt->execute([$nombre, $email, $password_hash])) {
                $success = 'Cuenta creada exitosamente. Ya puedes iniciar sesión.';
            } else {
                $error = 'Error al crear la cuenta. Inténtalo de nuevo.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Equipos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="login-body">
    <div class="login-container animate__animated animate__fadeIn">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <i class="fas fa-laptop logo-icon"></i>
                    <h1>Sistema de Equipos</h1>
                </div>
                <p>Crea tu cuenta</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success animate__animated animate__bounceIn">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="nombre">
                        <i class="fas fa-user"></i>
                        Nombre Completo
                    </label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <small>Mínimo 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i>
                        Confirmar Contraseña
                    </label>
                    <div class="password-input">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </form>

            <div class="login-footer">
                <a href="login.php" class="link">
                    <i class="fas fa-sign-in-alt"></i>
                    ¿Ya tienes cuenta? Inicia sesión
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Validación en tiempo real
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
