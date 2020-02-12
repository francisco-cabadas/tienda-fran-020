<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['confirmado']) && isset($_REQUEST['direccion'])){
    $pedidoId = DAO::carritoObtenerId($_SESSION["id"]);

    DAO::pedidoConfirmar($pedidoId, $_REQUEST['direccion']);
    redireccionar("pedido-finalizado.php");
}else{
    $pedidoId = DAO::carritoObtenerId($_SESSION["id"]);
    $cliente= DAO::clienteObtenerPorId($_SESSION["id"]);
    DAO::pedidoConfirmar($pedidoId, $cliente->getDireccion());
    redireccionar("pedido-finalizado.php");
}