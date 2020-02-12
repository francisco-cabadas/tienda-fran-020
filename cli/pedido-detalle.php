<?php

require_once "../_com/comunes-app.php";

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
        <th>Precio Unitario</th>
        <th>Precio Total Producto</th>
    </tr>

    <?php foreach ($productos as $producto) {
        $precioTotal += $producto["precio"]*$producto["unidades"];
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
            <td>
                <p><?=$producto["precio"]*$producto["unidades"]?></p>
            </td>
        </tr>
    <?php } ?>
    <tr style="font-weight: bold">
        <td>Precio Total Pedido</td>
        <td></td>
        <td></td>
        <td><p><?=$precioTotal?></p></td>
    </tr>
</table>
<a href="pedidos-listado.php">Volver al listado de pedidos</a> </br>
<a href="carrito-ver.php">Ir al carrito</a> </br>
<a href="productos-listado.php">Ir al listado de productos</a>
<?php require "../_com/info-sesion.php"; ?>

</body>

</html>
