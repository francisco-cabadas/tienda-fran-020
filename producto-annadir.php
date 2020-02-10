<?php
require_once "_clases.php";
require_once "_dao.php";
$nombre = $_REQUEST["nombre"];
$descripcion = $_REQUEST["descripcion"];
$precio = $_REQUEST["precio"];
$producto = new producto( NULL,$nombre, $descripcion, $precio);
echo("<p>se ha creado un producto
        </p><br><a href='productos-listado.php'>volver a la lista</a>");
?>

