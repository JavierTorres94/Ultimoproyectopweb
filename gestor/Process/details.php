<?php
    require 'Config/config.php';
    require 'Config/database.php';
    $db=new Database();
    $con=$db->conectar();

    $id=isset($_GET['id']) ? $_GET['id'] : '';
    $token=isset($_GET['token']) ? $_GET['token'] : '';

    if($id == '' || $token == '')
    {
        echo 'Error: No se envio informacion';
        exit;
    }
    else
    {
        $token_tmp=hash_hmac('sha1', $id, KEY_TOKEN);
        if($token==$token_tmp)
        {
            $sql=$con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1");
            $sql->execute([$id]);
            if($sql->fetchColumn() > 0)
            {
                $sql=$con->prepare("SELECT nombre, descripcion, precio, descuento FROM productos WHERE id=? AND activo=1 LIMIT 1");
                $sql->execute([$id]);
                $row=$sql->fetch(PDO::FETCH_ASSOC);
                $nombre=$row['nombre'];
                $descripcion=$row['descripcion'];
                $precio=$row['precio'];
                $descuento=$row['descuento'];
                $precio_descontado=$precio - (($precio * $descuento) / 100);
                $dir_images = 'Imagenes/Productos/' . $id . '/';

                $rutaImg = $dir_images . 'Main.jpg';

                if(!file_exists($rutaImg))
                {
                    $rutaImg = 'Imagenes/Productos/no_photo.png';
                }

                $images=array();
                if(file_exists($dir_images))
                {
                    $dir=dir($dir_images);

                    while(($archivo=$dir->read()) != false)
                    {
                        if($archivo != 'Main.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg')))
                        {
                            $images[]=$dir_images . $archivo;
                        }
                    }
                    $dir->close();
                }
            }
        }
        else
        {
            echo 'Error: Los tokens no corresponden';
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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
    <div class="container" style="padding: 30px 0;">
        <div class="row">
            <div class="col-md-6 order-md-1">
                <img src="<?php echo $rutaImg; ?>" class="d-block w-100" alt="Zapatos de color cafe">
            </div>
            <div class="col-md-6 order-md-2">
                <h2><?php echo $nombre; ?></h2>
                <?php if($descuento > 0) { ?>
                    <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del></p>
                    <h2>
                        <?php echo MONEDA . number_format($precio_descontado, 2, '.', ','); ?>
                        <small class="text-success"><?php echo $descuento; ?>% de descuento</small>
                    </h2>
                    <?php } else { ?>
                        <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>
                    <?php } ?>
                <p class="lead">
                    <?php echo $descripcion; ?>
                </p>
                <div class="d-grid gap-3 col-10 mx-auto">
                    <a href="checkout.php" class="btn btn-success" type="button " onclick="addProducto(<?php echo $id; ?>, '<?php echo $token_tmp; ?>')">Comprar</a>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    function addProducto(id, token)
    {
        let url='Clases/carrito.php';
        let formData=new FormData()
        formData.append('id', id)
        formData.append('token', token)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data => {
            if(data.ok)
            {
                let elemento=document.getElementById("num_cart")
                elemento.innerHTML=data.numero
            }
        } )
    }
</script>
</body>
</html>