<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "ggg";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nombre_usuario'])) {
    // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Obtener el ID de usuario y el nombre del usuario de la sesión
$idUsuario = $_SESSION['id_usuario'];
$nombreUsuario = $_SESSION['nombre_usuario'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/estilos.css"> 
</head>
<body class="container py-5" style="background-repeat: fill; background-image: linear-gradient(to bottom, rgb(255, 255, 255), rgb(255, 255, 255), rgb(188, 199, 198));"> 
   <div>
    <h1>Cuenta de <?php echo htmlspecialchars($nombreUsuario); ?></h1><br>
    <br><br>
    <section id="cambiar-datos" class="setting-section">
        <form class="mb-3 setting-form" action="#" method="POST">
            <div>
                <a href="inicio.php"><button class="btn btn-light" type="button">Regresar a Inicio</button></a>
                <input type="hidden" name="id_usuario" class="form-control" value="<?php echo htmlspecialchars($idUsuario);?>" required><br>
            </div>
            <div>
                <br><label for="nuevo-nombre" class="form-label">Nuevo Nombre: </label>
                <input type="text" name="nombre" class="form-control">
                <label for="nuevo-correo" class="form-label">Nuevo Teléfono:</label>
                <input type="tel" name="celular" class="form-control">
                <label for="nueva-contraseña" class="form-label">Nueva Contraseña:</label>
                <input type="password" name="contraseña" class="form-control">
                <label for="nuevo-correo" class="form-label">Nuevo Correo:</label>
                <input type="email" name="correo" class="form-control">
            </div>
            <div class="mb-3">
            </div>
            <button type="submit" class="btn btn-primary" name="Confirmar">Confirmar cambios</button>
            <button type="reset" class="btn btn-primary">Borrar</button>

            <?php
                if(isset($_POST['Confirmar'])) {
                    $id_usuario = $_POST['id_usuario'];
                    $nombre = $_POST['nombre'];
                    $celular = $_POST['celular'];
                    $contraseña = $_POST['contraseña'];
                    $correo = $_POST['correo'];

                    // Usar parámetros preparados para prevenir inyecciones SQL
                    $stmt = $enlace->prepare("UPDATE usuarios SET nombre=?, contraseña=?, celular=?, correo=? WHERE id_usuario=?");
                    $stmt->bind_param("ssssi", $nombre, $contraseña, $celular, $correo, $id_usuario);

                    if ($stmt->execute()) {
                        echo "Datos actualizados correctamente.";
                        // Redirigir al usuario a pag_principal.html
                        header("Location: pag_principal.html");
                        exit();
                    } else {
                        echo "Error al actualizar los datos: " . $stmt->error;
                    }
                    $stmt->close();
                }
            ?>
        </form>
    </section>

    <div>
    
    <section id="eliminar-cuenta" class="setting-section">
    <h2 class="mb-4"><strong>O deseas Eliminar tu cuenta?</strong></h2>
    <label for="correo" class="form-label">Ingrese su correo electrónico y contraseña para eliminar su cuenta: </label>
        <form action="#" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" class="form-control" required><br>
            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" class="form-control" required><br>
            <button type="submit" class="btn btn-danger" name="delete">Eliminar Cuenta</button>
            <?php
                if(isset($_POST['delete']))
                {
                    $correo = $_POST['correo'];
                    $contraseña = $_POST['contraseña'];

                    // Verificar si los datos ingresados coinciden con los registros en la base de datos
                    $stmt = $enlace->prepare("SELECT id_usuario FROM usuarios WHERE correo=? AND contraseña=?");
                    $stmt->bind_param("ss", $correo, $contraseña);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows == 1) {
                        // Si coincide, eliminar la cuenta
                        $stmt->bind_result($id_usuario);
                        $stmt->fetch();
                        $stmt->close();

                        $eliminar = "DELETE FROM usuarios WHERE id_usuario=$id_usuario";
                        $sql_delete = mysqli_query($enlace, $eliminar);

                        // Redirigir al usuario a pag_principal.html
                        header("Location: pag_principal.html");
                        exit();
                    } else {
                        echo "Los datos ingresados son incorrectos. Por favor, inténtelo de nuevo.";
                    }
                }
            ?>
        </form>
        <br>
    </section>
   </div>  
</body>
</html>
