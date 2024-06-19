<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AXxBaYnG36xR-s22u8RFN5RFkJDL_sakG93B6naG-klRDYyR51vRW-9jEdZC4u3o4pX2_0skJmbqx4np&currency=MXN"></script>

</head>
<body>
    <div id="paypal-button-container"></div>
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
                            amount: {value: 100}
                        }]
                    });
                },
                onApprove: function(data,actions)
                {
                    actions.order.capture().then(function(detalles)
                    {
                        window.location.href="completado.html";
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