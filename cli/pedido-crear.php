<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['confirmado'])){
    $pedidoId = DAO::carritoObtenerId($_SESSION["id"]);
    DAO::pedidoConfirmar($pedidoId);
    redireccionar("pedido-finalizado.php");
}