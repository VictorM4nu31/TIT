<?php
    session_start();
    require("conexion.php");

    // RECIBE LOS DATOS DE LA APP
    $usu = $_POST['username'];
    $cont = $_POST['password'];

    $validarusu = mysqli_query($conn, "SELECT * FROM asistente_trans_public WHERE 
    Usuario='$usu' AND Contrasena='$cont'");

    if (mysqli_num_rows($validarusu) > 0)
    {
        header ("location:combis/form-combi.html");
        exit;
    }else {
        echo '
            <script>
                alert("el usuario no existe");
                window.location = "checador.html"
            </script>
            ';
            exit;
    }
?>