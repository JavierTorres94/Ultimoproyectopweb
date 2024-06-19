<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "ggg";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_correo = $_POST['nombre_correo'];
    $contraseña = $_POST['contraseña'];

    // Consulta para verificar las credenciales usando parámetros preparados para prevenir inyecciones SQL
    $stmt = $enlace->prepare("SELECT id_usuario, nombre FROM usuarios WHERE (nombre = ? OR correo = ?) AND contraseña = ?");
    $stmt->bind_param("sss", $nombre_correo, $nombre_correo, $contraseña);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        // Inicio de sesión exitoso
        $usuario = $resultado->fetch_assoc();
        $_SESSION['nombre_usuario'] = $usuario['nombre'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        header("location: inicio.php");
        exit();
    } else {
        // Credenciales incorrectas
        echo "Nombre de usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login grandes galas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/estilos.css"> 
    <style>
        .center-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body class="center-content">

<figure class="text-center">
    <form method="post" action="login.php">
        <h1><strong>Inicia sesión</strong></h1><br>
        Nombre o correo electrónico <br>
        <input type="text" name="nombre_correo" maxlength="20" size="20" required> <br><br>
        Contraseña <br>
        <input type="password" name="contraseña" required> <br><br>
        <input type="submit" value="Aceptar"><br><br>
        No tienes una cuenta? <a href="registro.php">Regístrate</a>
    </form>
</figure>

</body>
</html>