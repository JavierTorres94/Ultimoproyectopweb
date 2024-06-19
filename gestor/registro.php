<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "ggg";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if (!$enlace) {
    die("Conexión fallida: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombreCliente = $_POST['nombreCliente'] ?? '';
    $correoCliente = $_POST['correoCliente'] ?? '';
    $telCliente = $_POST['telCliente'] ?? '';
    $passCliente = $_POST['passCliente'] ?? '';

    $stmt = $enlace->prepare("INSERT INTO usuarios (nombre, celular, correo, contraseña) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombreCliente, $telCliente, $correoCliente, $passCliente);

    if ($stmt->execute()) {
        $response = [
            'status' => 'success',
            'message' => 'Usuario registrado correctamente',
            'user_id' => $stmt->insert_id
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Error al registrar el usuario: ' . $stmt->error
        ];
    }

    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/estilos.css"> 
    <style>
        .center-content {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            margin-top: 80px;
        }
        p {
            width: 50%;
        }
        form {
            width: 30%;
        }
    </style>
</head>
<body class="center-content">
    <p>
        <br><br><br>
        <strong>¿Por qué registrarte en nuestro gestor de galas?</strong><br><br>
        
        <strong>Acceso exclusivo:</strong> Al registrarte, obtendrás acceso exclusivo a todas las funcionalidades <br> de nuestro gestor de galas, desde la creación de eventos hasta la gestión de invitados. <br>
        
        <strong>Organización simplificada:</strong> Con nuestra plataforma, podrás organizar tus galas <br> de manera fácil y eficiente. <br>
        
        <strong>Personalización total:</strong> Personaliza tus eventos según tus necesidades y preferencias, <br> tú tienes el control total. <br><br>
        
        <strong>¡Juntos podemos hacer que cada gala sea inolvidable!</strong><br>
        
        No pierdas más tiempo y únete a nosotros. <br><br>

        <strong>¡Regístrate ahora y comienza a planificar tus galas con estilo y facilidad!</strong>
    </p>

    <form action="#" name="Registro_de_usuario" method="post"><br>
        <h2><strong>Crear cuenta</strong></h2>
        Nombre <br><input type="text" name="nombreCliente" required><br>
        Correo <br><input type="email" name="correoCliente" required><br>
        Teléfono <br><input type="text" name="telCliente" required><br>
        Contraseña <br><input type="password" name="passCliente" required><br>
        La contraseña debe contener al menos seis caracteres. <br><br>

        <input type="submit" name="registro">
        <input type="reset"><br><br>

        Al crear una cuenta, aceptas las <strong>Condiciones de Uso <br> y el Aviso de Privacidad</strong> de Gestor de grandes galas (GGG). <br>
        ¿Ya tienes una cuenta? <a href="login.php">Inicia Sesion</a>
    </form>
</body>
</html>

<?php
if (isset($_POST['registro'])) {
    $nombreCliente = $_POST['nombreCliente'];
    $correoCliente = $_POST['correoCliente'];
    $telCliente = $_POST['telCliente'];
    $passCliente = $_POST['passCliente'];

    $stmt = $enlace->prepare("INSERT INTO usuarios (nombre, celular, correo, contraseña) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombreCliente, $telCliente, $correoCliente, $passCliente);

    if ($stmt->execute()) {
        // Iniciar sesión y almacenar el nombre y el ID del usuario en la sesión
        session_start();
        $_SESSION['nombre_usuario'] = $nombreCliente;
        $_SESSION['id_usuario'] = $stmt->insert_id;

        $stmt->close();
        // Redirigir a inicio.php
        header("Location: inicio.php");
        exit();
    } else {
        // Manejar error
        echo "Error: " . $stmt->error;
        $stmt->close();
    }
}
?>
