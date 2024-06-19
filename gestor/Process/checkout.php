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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
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
    <div class="container" style="padding-bottom: 556px;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Invitados</th>
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
                        <td><?php echo MONEDA . number_format($precio_descontado, 2, '.', ','); ?></td>
                        <td><input type="number" min="10" max="500" step="10" value="<?php echo $cantidad; ?>" size="5" id="cantidad_<?php echo $_id; ?>" onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)"></td>
                        <td>
                            <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]">
                                <?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?>
                            </div>
                        </td>
                        <td><a id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-toggle="modal" data-target="#eliminaModal">Eliminar</a></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2">
                            <p class="h3" id="total">
                                <?php echo MONEDA . number_format($total, 2, '.', ',');  ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
                    <?php } ?>
            </table>
        </div>
        <?php if($lista_carrito!=null)
        { ?>
            <div class="row">
                <div class="col-md-7 offset-md-10 d-grid gap-2">
                    <a href="pago.php" class="btn btn-primary btn-lg">Realizar pago</a>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="eliminaModal" tabindex="-1" role="dialog" aria-labelledby="eliminaModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="eliminaModalLabel">¡Alerta!</h5>
            </div>
            <div class="modal-body">
                ¿Desea eliminar el producto de la lista?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <form action="Clases/destroy.php" method="post">
                    <input id="btn-elimina" type="submit" class="btn btn-danger" name="Eliminar" value='Eliminar'>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    let eliminaModal=document.getElementById('eliminaModal')
    eliminaModal.addEventListener('show.bs.modal', function(event){
        let button=event.relatedTarget
        let id=button.getAttribute('data-bs-id')
        let buttonElimina=eliminaModal.querySelector('.modal-footer #btn-elimina')
        buttonElimina.value=id
    })

    function actualizaCantidad(cantidad, id)
    {
        let url='Clases/actualizar_carrito.php'
        let formData=new FormData()
        formData.append('action', 'agregar')
        formData.append('id', id)
        formData.append('cantidad', cantidad)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data => {
            if(data.ok)
            {
                let divsubtotal=document.getElementById('subtotal_' + id)
                divsubtotal.innerHTML=data.sub

                let total=0.00
                let list=document.getElementsByName('subtotal[]')

                for(let i=0; i < list.length; i++)
                {
                    total+=parseFloat(list[i].innerHTML.replace(/[$,]/g, ''))
                }

                total=new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2
                }).format(total)
                document.getElementById('total').innerHTML= '<?php echo MONEDA ?>' + total
            }
        } )
    }

    function eliminar()
    {
        let botonElimina=document.getElementById('btn-elimina')
        let id=botonElimina.value

        let url='Clases/actualizar_carrito.php'
        let formData=new FormData()
        formData.append('action', 'eliminar')
        formData.append('id', id)

        fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors'
        }).then(response => response.json())
        .then(data => {
            if(data.ok)
            {
                location.reload()
            }
        } )
    }
</script>
</body>
</html>