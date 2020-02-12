<?php

require_once "../_com/comunes-app.php";

$clienteId = $_SESSION["id"];
$pedidos = DAO::pedidosObtenerTodosPorCliente($clienteId);

?>



<html>

<head>
    <meta charset="UTF-8">
</head>

<br>
<h1>Listado de pedidos</h1>

<table border="1">

    <tr>
        <th>Código de pedido</th>
        <th>Dirección de envío</th>
        <th>Fecha de confirmación</th>
    </tr>

    <?php foreach ($pedidos as $pedido) {?>
        <tr>
            <td>
                <a href='pedido-detalle.php?id=<?=$pedido["id"]?>'><?=$pedido["codigo_pedido"]?></a>
            </td>
            <td>
                <a href='pedido-detalle.php?id=<?=$pedido["id"]?>'><?=$pedido["direccionEnvio"]?></a>
            <td>
                <a href='pedido-detalle.php?id=<?=$pedido["id"]?>'><?=$pedido["fechaConfirmacion"]?></a>
            </td>
        </tr>
    <?php } ?>

</table>
<a href="carrito-ver.php">Ir al carrito</a> </br>
<a href="productos-listado.php">Ir al listado de productos</a>
<?php require "../_com/info-sesion.php"; ?>

</body>

</html>