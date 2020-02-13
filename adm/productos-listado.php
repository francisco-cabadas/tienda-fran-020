<?php

require_once "../_com/comunes-app.php";

$productos = DAO::productoObtenerTodos();

?>



<html>

<head>
    <meta charset="UTF-8">
</head>

<body>
<h1>Listado de productos</h1>


<table border="1">

    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th></th>
    </tr>

    <?php foreach ($productos as $producto) { ?>
        <tr>
            <td>
                <a ><?=$producto->getNombre()?></a>
            </td>
            <td>
                <a ><?=$producto->generarPrecioFormateado()?></a>
            </td>
            <td>
                <a href='producto-detalle.php?id=<?=$producto->getId()?>'>Cambiar producto</a>
            </td>
        </tr>
    <?php } ?>

</table>

<a href="producto-annadir.php">Añadir producto</a>
<?php require "../_com/info-sesion.php"; ?>

</body>

</html>