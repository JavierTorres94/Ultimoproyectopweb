<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "ggg";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Obtener el ID de usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Obtener reservas del usuario actual
$reservas_query = "SELECT r.*, e.nombre_evento, p.titular, p.monto_total AS total_pagado 
                  FROM Reservas r 
                  INNER JOIN Eventos e ON r.id_evento = e.id_evento 
                  LEFT JOIN Pagos p ON r.id_usuario = p.id_usuario
                  WHERE r.id_usuario = $id_usuario";

$reservas_result = mysqli_query($enlace, $reservas_query);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/estilos.css"> 
</head>
<body>

<div class="container">
    <h1 class="text-center">Mis Eventos</h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID Reserva</th>
                <th scope="col">Evento</th>
                <th scope="col">Fecha</th>
                <th scope="col">Titular</th>
                <th scope="col">Total Pagado</th> <!-- Nuevo encabezado -->
                </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($reservas_result)): ?>
            <tr>
                <td><?php echo $row['id_reserva']; ?></td>
                <td><?php echo $row['nombre_evento']; ?></td>
                <td><?php echo $row['fecha']; ?></td>
                <td><?php echo $row['titular']; ?></td>
                <td>$<?php echo $row['total_pagado']; ?></td> <!-- Nuevo campo para mostrar el total pagado -->
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="imprimir_pdf.php" class="btn btn-primary">Imprimir PDF</a>
        <a href="inicio.php" class="btn btn-danger">Cancelar</a>
    </div>
</div>

</body>
</html>

<?php
mysqli_close($enlace);
?>
