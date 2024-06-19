<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "ggg";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener los datos del formulario
$tipoEvento = $_POST['tipoEvento'];
$fecha = $_POST['fecha'];
$nivel = $_POST['nivel'];
$aperitivos = isset($_POST['aperitivos']) ? implode(', ', $_POST['aperitivos']) : '';
$platoPrincipal = isset($_POST['platoPrincipal']) ? implode(', ', $_POST['platoPrincipal']) : '';
$postre = isset($_POST['postre']) ? implode(', ', $_POST['postre']) : '';
$musica = isset($_POST['musica']) ? implode(', ', $_POST['musica']) : '';

session_start();

if(isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
} else {
    // Si el usuario no ha iniciado sesión, redirigirlo al formulario de inicio de sesión
    header("Location: login.php");
    exit();
}

// Insertar los datos en la tabla Reservas
$sql = "INSERT INTO Reservas (id_usuario, id_evento, fecha, nivel_ingreso, aperitivos, plato_principal, postre, musica) 
        VALUES ('$id_usuario', '$tipoEvento', '$fecha', '$nivel', '$aperitivos', '$platoPrincipal', '$postre', '$musica')";

if (mysqli_query($enlace, $sql)) {
    // Redirigir a la página de pago
    header("Location: pago.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($enlace);
}

mysqli_close($enlace);
?>
