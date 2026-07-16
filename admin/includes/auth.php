<?php
require_once __DIR__ . '/../../config/config.php';

function usuarioAutenticado(): bool
{
    return !empty($_SESSION['usuario_id']);
}

function requerirLogin(): void
{
    if (!usuarioAutenticado()) {
        header('Location: login.php');
        exit;
    }
}

function requerirRol(array $roles): void
{
    requerirLogin();
    if (!in_array($_SESSION['usuario_rol'], $roles, true)) {
        http_response_code(403);
        die('No tienes permiso para acceder a esta sección.');
    }
}

function esAdmin(): bool
{
    return usuarioAutenticado() && $_SESSION['usuario_rol'] === 'admin';
}
