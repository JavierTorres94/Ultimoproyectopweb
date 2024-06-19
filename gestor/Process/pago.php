<?php
    require 'Config/config.php';
    require 'Config/database.php';
    $db=new Database();
    $con=$db->conectar();

    $productos=isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
    
    $lista_carrito=array();

    if($productos != null)
    {
        foreach($productos as $clave => $cantidad)
        {
            $sql=$con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id=? AND activo=1");
            $sql->execute([$clave]);
            $lista_carrito[]=$sql->fetch(PDO::FETCH_ASSOC);
        }
    }
    else
    {
        header("Location: index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body style="background-repeat: fill; background-image: linear-gradient(to bottom, rgb(255, 255, 255), rgb(255, 255, 255), rgb(188, 199, 198));">
<header>
    <div class="navbar navbar-dark bg-dark navbar-expand-lg">
        <div class="container d-flex justify-content-between">
            <a href="index.php" class="navbar-brand">Regresar</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="#" class="nav-link">Contactanos</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container" style="padding-bottom: 562px;">
        <div class="row">
            <div class="col-6">
                <h4>Detalles de pago</h4>
                <div id="paypal-button-container"></div>
            </div>
            <div class="col-6">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php if($lista_carrito==null)
                        {
                            echo '<tr><td colspan="5" class="text-center"><b>Lista Vacia</b></td></tr>';
                        }
                        else
                        {
                            $total=0;
                            foreach($lista_carrito as $producto)
                            {
                                $_id=$producto['id'];
                                $nombre=$producto['nombre'];
                                $precio=$producto['precio'];
                                $cantidad=$producto['cantidad'];
                                $descuento=$producto['descuento'];
                                $precio_descontado=$precio - (($precio * $descuento) / 100);
                                $subtotal=$cantidad * $precio_descontado;
                                $total+=$subtotal;
                        ?>
                                <tr>
                                    <td><?php echo $nombre; ?></td>
                                    <td>
                                        <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]">
                                            <?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="2">
                                        <p class="h3 text-right" id="total">
                                            <?php echo MONEDA . number_format($total, 2, '.', ',');  ?>
                                        </p>
                                    </td>
                                </tr>
                        <?php } ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency=<?php echo CURRENCY; ?>"></script>

<script>
    paypal.Buttons(
    {
        style:
        {
            color:'blue',shape:'pill',label:'pay'
        },
        createOrder: function(data,actions)
        {
            return actions.order.create(
            {
                purchase_units: [
                {
                    amount: {value: <?php echo $total; ?>}
                }]
            });
        },
        onApprove: function(data,actions)
        {
            let URL='Clases/captura.php'
            actions.order.capture().then(function(detalles)
            {
                console.log(detalles)

                return fetch(url, {
                    method: 'post',
                    headers: {
                        'content-type': 'application/json'
                    },
                    body: JSON.stringify({
                        detalles: detalles
                    })
                })
            });
        },
        onCancel: function(data)
        {
            alert("Pago cancelado")
            console.log(data);
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>