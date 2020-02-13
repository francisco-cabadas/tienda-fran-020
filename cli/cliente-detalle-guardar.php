<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['sobreescribirDireccion']) && isset($_REQUEST['direccion']))
{
    DAO::clienteActualizarDireccion($_REQUEST['direccion']);
    //redireccionar($_SERVER['HTTP_REFERER']); // volver a la pagina que me llamo
    redireccionar("pedido-crear.php?confirmado=true&direccion=" . $_REQUEST['direccion']);
} else if (isset($_REQUEST['direccion'])!="" && !isset($_REQUEST['sobreescribirDireccion'])) {
    redireccionar("pedido-crear.php?confirmado=true&direccion=" . $_REQUEST['direccion']);
} else {
    redireccionar("pedido-crear.php?confirmado=true&direccion=''");
}