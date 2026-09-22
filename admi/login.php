<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\PdoFactory;

$pdo = PdoFactory::createForAdmin();

$nombre = trim((string) ($_POST['nombre'] ?? ''));
$contrasena = (string) ($_POST['contrasena'] ?? '');

if ($nombre === '' || $contrasena === '') {
    echo '<script>alert("Datos incompletos");window.location="administrador.html"</script>';
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre, contrasena FROM admin_tb WHERE nombre = :nombre LIMIT 1');
$stmt->execute(['nombre' => $nombre]);
$row = $stmt->fetch();

$authOk = false;
if (\is_array($row) && isset($row['contrasena'])) {
    $hash = (string) $row['contrasena'];
    $info = password_get_info($hash);
    if ($info['algo'] !== 0) {
        $authOk = password_verify($contrasena, $hash);
    } else {
        $authOk = hash_equals($hash, $contrasena);
    }
}

if ($authOk) {
    session_regenerate_id(true);
    $_SESSION['nombre'] = $nombre;
    $_SESSION['admin_id'] = \is_array($row) ? ($row['id'] ?? null) : null;
    header('Location: ../registr_pers/registro.php');
    exit;
}

echo '<script>alert("el usuario no existe o contraseña incorrecta");window.location="administrador.html"</script>';
