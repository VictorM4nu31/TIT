<?php
// Establecer la conexión con la base de datos
$servername = "localhost";
$database = "tit_bd2";
$username = "root";
$password = "";

$conn=mysqli_connect("$servername","$username","$password","$database");

// Obtener los datos del formulario
$id_transporte = $_POST['id_transporte'];
$ruta = $_POST['ruta'];
$hr_sal = $_POST['hr_sal'];
$fecha = $_POST['fecha'];
$id_conductor = $_POST['id_conductor'];
$id_checador = $_POST['id_checador'];

// Insertar los datos en la tabla
$sql= "INSERT INTO hora_sal_tb(id_trans, ruta, hr_sal, fecha, id_conductor, id_checador) VALUES('$id_transporte','$ruta', '$hr_sal','$fecha','$id_conductor','$id_checador')";

if(mysqli_query($conn, $sql)){
	
    echo   '<script>
                alert("registro exitoso");
                window.location = "horario_combi.html"
            </script>';
        exit;
}else{
    echo '
            <script>
                alert("vuelve a intentarlo");
                window.location = "horario_combi.html"
            </script>
            ';
            exit;
}
// Cerrar la conexión
mysqli_close($conn);
?>