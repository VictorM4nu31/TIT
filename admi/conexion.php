<?php
$servername = "localhost"; //nombre del servidor
$username = "root"; //nombre de usuario de la base de datos
$password = ""; //contraseña de la base de datos
$dbname = "bd_tranzit"; //nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
/*
if ($conn->connect_error) {
  die("La conexión falló: " . $conn->connect_error);
}
echo "Conexión exitosa";
*/
?>
