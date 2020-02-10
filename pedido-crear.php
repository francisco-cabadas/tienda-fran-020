<?php

require_once "_clases.php";
require_once "_dao.php";
require_once "_sesiones.php";
require_once "_utilidades.php";
garantizarSesion();

if (isset($_REQUEST['confirmado'])){
    $pedidoId = DAO::carritoObtenerPedidoIdPorCliente($_SESSION["id"]);
    DAO::pedidoConfirmar($pedidoId);
    redireccionar("pedido-finalizado.php");
}