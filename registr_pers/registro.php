<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['nombre'])) {
    header('Location: ../admi/administrador.html');
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\PdoFactory;

$pdo = PdoFactory::create();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo '<script>alert("Metodo no permitido");window.location="registro.html"</script>';
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$lastname = trim((string) ($_POST['lastname'] ?? ''));
$ageRaw = trim((string) ($_POST['age'] ?? ''));
$gender = trim((string) ($_POST['gender'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$errors = [];

if ($name === '' || mb_strlen($name) > 100) {
    $errors[] = 'Nombre invalido';
}

if ($lastname === '' || mb_strlen($lastname) > 100) {
    $errors[] = 'Apellido invalido';
}

$age = filter_var($ageRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 18, 'max_range' => 99]]);
if ($age === false) {
    $errors[] = 'Edad invalida';
}

if (!\in_array($gender, ['male', 'female', 'other'], true)) {
    $errors[] = 'Genero invalido';
}

if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = 'Correo invalido';
}

if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
    $errors[] = 'Telefono invalido';
}

if (mb_strlen($password) < 8) {
    $errors[] = 'Contrasena minima 8 caracteres';
}

if ($errors !== []) {
    $msg = htmlspecialchars(implode(', ', $errors), ENT_QUOTES, 'UTF-8');
    echo '<script>alert("' . $msg . '");window.location="registro.html"</script>';
    exit;
}

$hash = password_hash($password, PASSWORD_ARGON2ID);

if (!\is_string($hash)) {
    echo '<script>alert("Error interno");window.location="registro.html"</script>';
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO checador_tb (nombre, apellido, edad, sexo, numero_telefono, correo, `contraseña`) '
    . 'VALUES (:nombre, :apellido, :edad, :sexo, :telefono, :correo, :hash)',
);
$result = $stmt->execute([
    'nombre' => $name,
    'apellido' => $lastname,
    'edad' => $age,
    'sexo' => $gender,
    'telefono' => $phone,
    'correo' => $email,
    'hash' => $hash,
]);

if ($result) {
    echo '<script>alert("Usuario registrado correctamente");window.location="registro.html"</script>';
    exit;
}

echo '<script>alert("Usuario registrado incorrectamente");window.location="registro.html"</script>';
