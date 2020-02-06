<?php
session_start();
require_once "_clases.php";
require_once "_dao.php";
$sesioniniciada= DAO::inicioSesion();
if(!$sesioniniciada){
     header("location: inicio-sesion.php");
}else {
$productos = DAO::productoObtenerTodos();

?>
<!-- -->


<html>

<head>
    <meta charset="UTF-8">
</head>

<body>

<table border="1">

    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Añadir</th>
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
                <a href='carrito-añadir-producto.php?productoId=<?=$producto->getId()?>'>Al carrito</a>
            </td>
        </tr>
    <?php }

    ?>

</table>
<a href="cerrar-sesion.php">cerrar sesion</a>

</body>

</html>
    <?php
}
?>