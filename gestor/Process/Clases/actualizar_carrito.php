<?php

require '../Config/config.php';
require '../Config/database.php';

if(isset($_POST['action']))
{
    $action=$_POST['action'];
    $id=isset($_POST['id']) ? $_POST['id'] : 0;

    if($action=='agregar')
    {
        $cantidad=isset($_POST['cantidad']) ? $_POST['cantidad'] : 0;
        $respuesta=agregar($id, $cantidad);
        if($respuesta>0)
        {
            $datos['ok']=true;
        }
        else
        {
            error_log("Error 1");
            $datos['ok']=false;
        }
        $datos['sub']= MONEDA . number_format($respuesta, 2, '.', ',');
    }
    else if($action=='eliminar')
    {
        $datos['ok']=eliminar($id);
    }
    else
    {
        error_log("Error 2");
        $datos['ok']=false;
    }
}
else
{
    error_log("Error 3");
    $datos['ok']=false;
}

echo json_encode($datos);

function agregar($id, $cantidad)
{
    $res=0;
    if($id > 0 && $cantidad > 0 && is_numeric(($cantidad)))
    {
        if(isset($_SESSION['carrito']['productos'][$id]))
        {
            $_SESSION['carrito']['productos'][$id]=$cantidad;

            $db=new Database();
            $con=$db->conectar();
            $sql=$con->prepare("SELECT precio, descuento FROM productos WHERE id=? AND activo=1 LIMIT 1");
            $sql->execute([$id]);
            $row=$sql->fetch(PDO::FETCH_ASSOC);
            $precio=$row['precio'];
            $descuento=$row['descuento'];
            $precio_descontado=$precio - (($precio * $descuento) / 100);
            $res=$cantidad * $precio_descontado;

            return $res;
        }
    }
    else
    {
        return $res;
    }
}

function eliminar($id)
{
    if($id > 0)
    {
        if(isset($_SESSION['carrito']['productos'][$id]))
        {
            unset($_SESSION['carrito']['productos'][$id]);
            return true;
        }
    }
    else
    {
        error_log("Error 4");
        return false;
    }
}

?>