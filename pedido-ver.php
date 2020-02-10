<?php

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();

$clienteId = $_SESSION["id"];
$pedidos = DAO::pedidosObtenerTodosPorCliente($clienteId);

?>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>
<h1>Listado de pedidos</h1>

<table border="1">

    <tr>
        <th>Código de pedido</th>
        <th>Dirección de envío</th>
        <th>Fecha de confirmación</th>
    </tr>

    <?php foreach ($pedidos as $pedido) { ?>
        <tr>
            <td>
                <a href='pedido-detalle.php?id=<?=$pedido["id"]?>'><?=$pedido["id"]?></a>
            </td>
            <td>
                <p><?=$pedido["direccionEnvio"]?></p>
            </td>
            <td>
                <p><?=$pedido["fechaConfirmacion"]?></p>
            </td>
        </tr>
    <?php } ?>

</table>
<a href="carrito-ver.php">Ir al carrito</a>
<a href="productos-listado.php">Ir al listado de productos</a>
<?php require "_info-sesion.php"; ?>

</body>

</html>