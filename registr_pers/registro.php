<?php

$conn=mysqli_connect("localhost", "root", "", "tit_bd2");

// RECIBE LOS DATOS DE LA APP
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];


$query = "INSERT INTO `checador_tb`(`nombre`, `apellido`, `edad`, `sexo`, `numero_telefono`, `correo`, `contraseña`)
    VALUES('$name', '$lastname', '$age', '$gender', '$email', '$phone', '$password')";

$resultado = mysqli_query($conn, $query);


if ($resultado) {
    echo '
            <script>
                alert("Usuario registrado correctamente");
                window.location = "registro.html";
            </script>';
} else {
    echo '
            <script>
                alert("Usuario registrado incorrectamente");
                window.location = "registro.html";
            </script>';
}

$mysqli_close($conn);

?>