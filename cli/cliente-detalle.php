<?php

require_once "../_com/comunes-app.php";

$id = $_SESSION["id"];

$cliente = DAO::clienteObtenerPorId($id);

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
<p>Email <?=$cliente->getEmail()?></p>
<p>Nombre <?=$cliente->getNombre()?></p>
<p>Direccion <?=$cliente->getDireccion()?></p>
<p>Telefono <?=$cliente->getTelefono()?></p>

<a href="productos-listado.php">Volver listado</a>
<a href="pedido-ver.php">Ver Pedidos</a>
<a href="carrito-ver.php">ver carrito</a>
</body>

<?php require "../_com/info-sesion.php"; ?>

</html>