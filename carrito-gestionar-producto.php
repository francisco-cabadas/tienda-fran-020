<?php
require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";
garantizarSesion();


$clienteId = 1; //$_SESSION["id"]; // Lo dejamos en 1 considerando que es el usuario "jlopez"
$productoId = $_REQUEST["productoId"];
$variacionUnidades = $_REQUEST["variacionUnidades"];

$carrito = DAO::carritoObtenerParaCliente($clienteId);

$carrito->variarProducto($productoId, $variacionUnidades);
redireccionar("productos-listado.php?agregado");

?>