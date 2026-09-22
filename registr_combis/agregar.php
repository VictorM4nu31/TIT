<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\PdoFactory;

$pdo = PdoFactory::create();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo '<script>alert("Metodo no permitido");window.location="horario_combi.html"</script>';
    exit;
}

$idTransporte = trim((string) ($_POST['id_transporte'] ?? ''));
$ruta = trim((string) ($_POST['ruta'] ?? ''));
$hrSal = trim((string) ($_POST['hr_sal'] ?? ''));
$fecha = trim((string) ($_POST['fecha'] ?? ''));
$idConductor = trim((string) ($_POST['id_conductor'] ?? ''));
$idChecador = trim((string) ($_POST['id_checador'] ?? ''));

if (
    $idTransporte === '' || $ruta === '' || $hrSal === ''
    || $fecha === '' || $idConductor === '' || $idChecador === ''
) {
    echo '<script>alert("Datos incompletos");window.location="horario_combi.html"</script>';
    exit;
}

if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hrSal)) {
    echo '<script>alert("Hora invalida");window.location="horario_combi.html"</script>';
    exit;
}

$dateObj = DateTimeImmutable::createFromFormat('Y-m-d', $fecha);
if ($dateObj === false || $dateObj->format('Y-m-d') !== $fecha) {
    echo '<script>alert("Fecha invalida");window.location="horario_combi.html"</script>';
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO hora_sal_tb (id_trans, ruta, hr_sal, fecha, id_conductor, id_checador) '
    . 'VALUES (:id_trans, :ruta, :hr_sal, :fecha, :id_conductor, :id_checador)',
);

try {
    $ok = $stmt->execute([
        'id_trans' => $idTransporte,
        'ruta' => $ruta,
        'hr_sal' => $hrSal,
        'fecha' => $fecha,
        'id_conductor' => $idConductor,
        'id_checador' => $idChecador,
    ]);
} catch (PDOException $e) {
    $ok = false;
}

if ($ok) {
    echo '<script>alert("registro exitoso");window.location="horario_combi.html"</script>';
    exit;
}

echo '<script>alert("vuelve a intentarlo");window.location="horario_combi.html"</script>';
