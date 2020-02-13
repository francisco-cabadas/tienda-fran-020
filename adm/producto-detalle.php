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
    <p>Nombre producto<?=$producto->getNombre()?></p>
    <p>Descripción<?=$producto->getDescripcion()?></p>




    <?php //If (usuario==admin){?>
        <form action="producto-detalle-guardar.php">
            Realizar cambios en el producto:
            <input type="text" name="productoId" value="<?=$id?>">
            Nuevo nombre:<input type="text" name="nombre"><br>
            Nueva descripion:<input type="text" name="descripcion"><br>
            Nuevo precio:<input type="number" name="precio"><br>
        <!-- TODO Actualizar stock:<input type="number" name="stock"> Sobreescribe la cantidad de stock, sumar y restar stock actual-->
            <input type="submit">
        </form>
    <?php //}?>

    <a href="productos-listado.php">Volver listado</a>
</body>
</html>
