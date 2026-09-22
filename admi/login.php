<?php
include "conexion.php";

// Obtener los datos del formulario
$nombre = $_POST["nombre"];
$contrasena = $_POST["contrasena"];

// Consultar la tabla de usuarios
$validarusu = mysqli_query($conn,"SELECT * FROM admin_tb WHERE nombre = '$nombre' AND contrasena = '$contrasena'");

// Verificar si se encontró un usuario con los datos ingresados

if (mysqli_num_rows($validarusu) > 1)
    {
        header ("location: registr_pers/registro.html");
        exit;
    }else {
        echo '
            <script>
                alert("el usuario no existe");
                window.location = "administrador.html"
            </script>
            ';
            exit;
    }
?>
