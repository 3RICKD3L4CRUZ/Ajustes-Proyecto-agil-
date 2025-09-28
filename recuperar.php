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
    
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Por favor ingresa tu correo electrónico';
    } else {
        // Verificar si el email existe
        $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            // Generar token de recuperación
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $stmt = $pdo->prepare("INSERT INTO tokens_recuperacion (usuario_id, token, expira, usado) VALUES (?, ?, ?, 0) ON DUPLICATE KEY UPDATE token = ?, expira = ?, usado = 0");
            $stmt->execute([$usuario['id'], $token, $expira, $token, $expira]);
            
            // Aquí normalmente enviarías un email
            // Por ahora solo mostramos el mensaje de éxito
            $success = 'Se ha enviado un enlace de recuperación a tu correo electrónico.';
            
            // En un entorno real, aquí enviarías el email con el enlace:
            // $enlace = "http://tudominio.com/nueva_password.php?token=" . $token;
        } else {
            $error = 'No se encontró una cuenta con ese correo electrónico';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Sistema de Equipos</title>
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
                <p>Recuperar contraseña</p>
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
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="Ingresa tu correo electrónico">
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Enlace de Recuperación
                </button>
            </form>

            <div class="login-footer">
                <a href="login.php" class="link">
                    <i class="fas fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>
                <a href="registro.php" class="link">
                    <i class="fas fa-user-plus"></i>
                    ¿No tienes cuenta? Regístrate
                </a>
            </div>
        </div>
    </div>
</body>
</html>
