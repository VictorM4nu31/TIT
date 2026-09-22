<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\PdoFactory;

$pdo = PdoFactory::create();

$usu = trim((string) ($_POST['username'] ?? ''));
$cont = (string) ($_POST['password'] ?? '');

if ($usu === '' || $cont === '') {
    echo '<script>alert("Datos incompletos");window.location="checador.html"</script>';
    exit;
}

$stmt = $pdo->prepare('SELECT id, Usuario, Contrasena FROM asistente_trans_public WHERE Usuario = :usu LIMIT 1');
$stmt->execute(['usu' => $usu]);
$row = $stmt->fetch();

$authOk = false;
if (\is_array($row) && isset($row['Contrasena'])) {
    $hash = (string) $row['Contrasena'];
    $info = password_get_info($hash);
    if ($info['algo'] !== 0) {
        $authOk = password_verify($cont, $hash);
    } else {
        $authOk = hash_equals($hash, $cont);
    }
}

if ($authOk) {
    session_regenerate_id(true);
    $_SESSION['checador'] = $usu;
    $_SESSION['checador_id'] = \is_array($row) ? ($row['id'] ?? null) : null;
    header('Location: ../registr_combis/horario_combi.html');
    exit;
}

echo '<script>alert("el usuario no existe o contraseña incorrecta");window.location="checador.html"</script>';
