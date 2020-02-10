<?php

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();

$clienteId = $_SESSION["id"];
$pedidoId = $_REQUEST["id"];
$productos = DAO::pedidoObtenerProductos($pedidoId);


$precioTotal = 0;
?>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>
<h1>Detalle del pedido</h1>

<table border="1">

    <tr>
        <th>Nombre del producto</th>
        <th>Unidades</th>
        <th>Precio</th>
    </tr>

    <?php foreach ($productos as $producto) {
        $precioTotal += $producto["precio"];
        ?>
        <tr>
            <td>
                <p><?=$producto["nombre"]?></p>
            </td>
            <td>
                <p><?=$producto["unidades"]?></p>
            </td>
            <td>
                <p><?=$producto["precio"]?></p>
            </td>
        </tr>
    <?php } ?>
    <tr style="font-weight: bold">
        <td>Precio Total</td>
        <td></td>
        <td><p><?=$precioTotal?></p></td>
    </tr>
</table>
<a href="carrito-ver.php">Ir al carrito</a>
<a href="productos-listado.php">Ir al listado de productos</a>
<?php require "_info-sesion.php"; ?>

</body>

</html>
