<?php

require_once "../_com/comunes-app.php";

$nombre = $_REQUEST["nombre"];
$descripcion = $_REQUEST["descripcion"];
$precio = $_REQUEST["precio"];
$producto = new producto( NULL,$nombre, $descripcion, $precio);

// TODO Echo a secas no. Hacer todo el <html>...
echo("<p>se ha creado un producto
        </p><br><a href='../cli/productos-listado.php'>volver a la lista</a>");