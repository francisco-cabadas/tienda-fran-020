<?php

require_once "_clases.php";
require_once "_dao.php";

$id = $_REQUEST["id"];

$producto = (DAO::productoObtenerPorId($id));

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Nombre producto<?=$producto->getNombre()?></p>
    <p>Descripción<?=$producto->getDescripcion()?></p>

    <a href="productos-listado.php">Volver listado</a>

    <form action="carrito-gestionar-producto.php?productoId=<?=$producto->getId()?>" method="post">
        <input type="number" min="1" value="1" name="variacionUnidades">
        <input type="submit" name="Añadir carrito" value="annadir">
    </form>
    <?php //If (usuario==admin){?>
        <form action="producto-detalle-guardar.php">
            <input type="hidden" value="<?=$id?>" name="productoId">
            Realizar cambios en el producto:
            Nuevo nombre:<input type="text" name="nombre">
            Nueva descripion:<input type="text" name="descripcion">
            Nuevo precio:<input type="number" name="precio">
        <!-- TODO Actualizar stock:<input type="number" name="stock"> Sobreescribe la cantidad de stock, sumar y restar stock actual-->
            <input type="submit">
        </form>
    <?php //}?>
</body>
</html>
