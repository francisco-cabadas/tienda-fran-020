<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['actualizarDireccion'])){
    DAO::clienteActualizarDireccion($_REQUEST['direccion']);
    redireccionar($_SERVER['HTTP_REFERER']); // volver a la pagina que me llamo
}

