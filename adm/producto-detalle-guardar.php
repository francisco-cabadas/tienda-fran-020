<?php

require_once "../_com/comunes-app.php";

$id = $_REQUEST["productoId"];
$producto = DAO::productoObtenerPorId($id);
$nuevoNombre = $_REQUEST["nombre"];
$nuevaDescripcion = $_REQUEST["descripcion"];
$nuevoPrecio = $_REQUEST["precio"];

DAO::productoActualizar($id,$nuevoNombre,$nuevaDescripcion,$nuevoPrecio/*,$actualizaStock*/);
?>



<html>
<body>
<p>se ha actualizado correctamente el producto
</p><br><a href='productos-listado.php'>volver a la lista</a>
</body>
</html>

