<?php

require_once "../_com/comunes-app.php";

$nombre = $_REQUEST["nombre"];
$descripcion = $_REQUEST["descripcion"];
$precio = $_REQUEST["precio"];
$producto = new producto( NULL,$nombre, $descripcion, $precio);
?>


<html>
<body>
<p>se ha creado correctamente el producto
</p><br><a href='productos-listado.php'>volver a la lista</a>
</body>
</html>


