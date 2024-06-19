<?php
    define("CLIENT_ID", "AXxBaYnG36xR-s22u8RFN5RFkJDL_sakG93B6naG-klRDYyR51vRW-9jEdZC4u3o4pX2_0skJmbqx4np");
    define("CURRENCY", "MXN");
    define("KEY_TOKEN", "MAR.ieFa-684");
    define("MONEDA", "$");

    session_start();

    $num_cart=0;
    if(isset($_SESSION['carrito']['productos']))
    {
        $num_cart=count($_SESSION['carrito']['productos']);
    }
?>