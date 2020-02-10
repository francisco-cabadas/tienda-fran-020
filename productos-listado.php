<?php

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();

$productos = DAO::productoObtenerTodos();

?>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>
<h1>Listado de productos</h1>
<?php
if(isset($_REQUEST["agregado"])){
echo "<p style='color: green'>Producto agregado con exito al carrito</p>";
}
?>

<table border="1">

    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th></th>
    </tr>

    <?php foreach ($productos as $producto) { ?>
        <tr>
            <td>
                <a href='producto-detalle.php?id=<?=$producto->getId()?>'><?=$producto->getNombre()?></a>

            </td>
            <td>
                <a href='producto-detalle.php?id=<?=$producto->getId()?>'><?=$producto->generarPrecioFormateado()?></a>
            </td>
            <td>
                <a href='carrito-gestionar-producto.php?productoId=<?=$producto->getId()?>&agregar=true'>Comprar</a>
            </td>
        </tr>
    <?php } ?>

</table>
<a href="carrito-ver.php">Ir al carrito</a>
<a href='cliente-detalle.php'>Ver perfil</a>
<?php require "_info-sesion.php"; ?>

</body>

</html>