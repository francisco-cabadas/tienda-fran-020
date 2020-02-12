<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['confirmado'])){
    $pedidoId = DAO::carritoObtenerId($_SESSION["id"]);
    DAO::pedidoConfirmar($pedidoId);
    $codigoPedido = generarCadenaAleatoria(8);
    DAO::ejecutarActualizacion("UPDATE pedido SET codigoPedido = ? WHERE id = ?",[$codigoPedido, $pedidoId]);
    redireccionar("pedido-finalizado.php");
}