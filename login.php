<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_POST) {
    include 'config/database.php';
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email'] = $usuario['email'];
            
            // Registrar último acceso
            $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $stmt->execute([$usuario['id']]);
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Credenciales incorrectas';
        }
    } else {
        $error = 'Por favor complete todos los campos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Equipos</title>
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
                <p>Inicia sesión para continuar</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
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
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>

            <div class="login-footer">
                <a href="registro.php" class="link">
                    <i class="fas fa-user-plus"></i>
                    ¿No tienes cuenta? Regístrate
                </a>
                <a href="recuperar.php" class="link">
                    <i class="fas fa-key"></i>
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        // Animación de entrada para los elementos del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                setTimeout(() => {
                    group.classList.add('animate__animated', 'animate__fadeInUp');
                }, index * 100);
            });
        });
    </script>
</body>
</html>
