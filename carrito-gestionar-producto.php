<?php

$clienteId = $_SESSION["id"];
$productoId = $_REQUEST["id"];
$variacionUnidades = $_REQUEST["variacionUnidades"];

$carrito = DAO::carritoObtenerParaCliente($clienteId);


?>