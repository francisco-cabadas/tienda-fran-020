<?php

require_once "../_com/comunes-app.php";

$id = $_REQUEST["id"];

$producto = DAO::productoObtenerPorId($id);

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
    <form>
        <table border="1">
            <tr><td><div><label>NOMBRE</label><td><input value="<?=$producto->getNombre()?>"></div></td></tr>
            <tr><td><div><label>DESCRIPCIÓN</label><td><input value="<?=$producto->getDescripcion()?>"></div></td></tr>
            <tr><td><div><label>PRECIO</label><td><input value="<?=$producto->getPrecio()?>"></div></td></tr>

    </form>

    <form action="carrito-gestionar-producto.php" method="get">
        <tr><td><label>UNIDADES: </label><input type="hidden" name="productoId" value="<?=$producto->getId()?>"></td>
        <td><input type="number" min="1" value="1" name="variacionUnidades"></td></tr>
        </table>
        <input type="submit" name="agregar" value="añadir">

    </form>
</body>

<?php require "../_com/info-sesion.php"; ?>

</html>
