<?php
require_once "_clases.php";
require_once "_dao.php";
$id = $_REQUEST["productoId"];
$producto = DAO::productoObtenerPorId($id);
$nuevoNombre = $_REQUEST["nombre"];
$nuevaDescripcion = $_REQUEST["descripcion"];
$nuevoPrecio = $_REQUEST["precio"];

$producto->actualizarProducto($id,$nuevoNombre,$nuevaDescripcion,$nuevoPrecio/*,$actualizaStock*/);
echo("<p>se ha actualizado correctamente
        </p><br><a href='productos-listado.php'>volver a la lista</a>");

