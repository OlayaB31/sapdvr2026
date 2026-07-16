<?php
require_once __DIR__ . '/../config/config.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Ingresa tu usuario y contraseña.';
    } else {
        $pdo = conectarDB();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario AND activo = 1 LIMIT 1');
        $stmt->execute(['usuario' => $usuario]);
        $fila = $stmt->fetch();

        if ($fila && password_verify($password, $fila['password'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombre'];
            $_SESSION['usuario_rol'] = $fila['rol'];
            header('Location: dashboard.php');
            exit;
        }

        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso al panel — SAPD</title>
<link rel="icon" href="data:,">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<div class="login-envoltorio">
    <div class="login-caja">
        <div class="marca">
            <svg width="34" height="34" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 2 L44 10 V22 C44 34 36 42 24 46 C12 42 4 34 4 22 V10 Z" fill="#16213a" stroke="#c9a227" stroke-width="2"/>
                <path d="M24 9 L24 27 M15 18 L33 18" stroke="#c9a227" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
            <div>
                <h1>Panel interno</h1>
                <span class="sub">SAPD</span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="campo">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required autofocus value="<?= e($_POST['usuario'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-dorado" style="width: 100%; justify-content: center;">Ingresar</button>
        </form>
    </div>
</div>
</body>
</html>
