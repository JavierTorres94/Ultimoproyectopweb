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
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}

// Obtener el ID de usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Obtener los datos del formulario de pago
$tipoPago = $_POST['nivel'];
$titular = $_POST['nombre'];
$numeroTarjeta = $_POST['texto1'] . $_POST['texto2'] . $_POST['texto3'] . $_POST['texto4'];
$fechaExpiracion = $_POST['expira'];
$codigoSeguridad = $_POST['crv'];
$montoTotal = 1234.56; // Total fijo

// Validar que los campos obligatorios no estén vacíos
if (empty($tipoPago) || empty($titular) || empty($numeroTarjeta) || empty($fechaExpiracion) || empty($codigoSeguridad)) {
    echo "<script>
            alert('Todos los campos son obligatorios.');
            window.history.back();
          </script>";
    exit();
}

$tipoPagoTexto = '';
if ($tipoPago == '1') {
    $tipoPagoTexto = 'Débito';
} elseif ($tipoPago == '2') {
    $tipoPagoTexto = 'Crédito';
} elseif ($tipoPago == '3') {
    $tipoPagoTexto = 'PayPal';
}

function encryptData($data, $key) 
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

$key = 'your-encryption-key'; // Use a secure key
$numeroTarjetaEncrypted = encryptData($numeroTarjeta, $key);
$codigoSeguridadEncrypted = encryptData($codigoSeguridad, $key);

// Insert encrypted data
$sql = "INSERT INTO Pagos (id_usuario, tipo_pago, titular, numero_tarjeta, fecha_expiracion, codigo_seguridad, monto_total) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($enlace, $sql)) {
    mysqli_stmt_bind_param($stmt, "isssssd", $id_usuario, $tipoPagoTexto, $titular, $numeroTarjetaEncrypted, $fechaExpiracion, $codigoSeguridadEncrypted, $montoTotal);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Su pago se ha realizado con éxito');
                window.location.href = 'inicio.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($enlace);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($enlace);
?>
