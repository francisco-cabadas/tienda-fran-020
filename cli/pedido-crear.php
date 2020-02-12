<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['confirmado']) && $_REQUEST['direccion'] == ""){
    $pedidoId = DAO::carritoObtenerId($_SESSION["id"]);
    $cliente = DAO::clienteObtenerPorId($_SESSION["id"]);
    DAO::pedidoConfirmar($pedidoId, $cliente->getDireccion());
    redireccionar("pedido-finalizado.php?1=1"); // TODO ¿Cómo es esto de 1=1? Explicar y/o cambiar el código para que ayude a entender la cosa.
} else {
    $pedidoId = DAO::carritoObtenerId($_SESSION["id"]);
    DAO::pedidoConfirmar($pedidoId, $_REQUEST['direccion']);
    redireccionar("pedido-finalizado.php?1=2&2=" . $_REQUEST['direccion']); // TODO ¿Cómo es esto de 1=2 y 2=dirección? Explicar y/o cambiar el código para que ayude a entender la cosa.
}
