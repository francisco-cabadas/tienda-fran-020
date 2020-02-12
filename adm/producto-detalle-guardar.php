<?php

require_once "../_com/comunes-app.php";

$id = $_REQUEST["productoId"];
$producto = DAO::productoObtenerPorId($id);
$nuevoNombre = $_REQUEST["nombre"];
$nuevaDescripcion = $_REQUEST["descripcion"];
$nuevoPrecio = $_REQUEST["precio"];

$producto->actualizarProducto($id,$nuevoNombre,$nuevaDescripcion,$nuevoPrecio/*,$actualizaStock*/);

// TODO: poner <html> y todo para esto, no un echo y ya.
echo("<p>se ha actualizado correctamente
        </p><br><a href='../cli/productos-listado.php'>volver a la lista</a>");

