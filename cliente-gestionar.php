<?php

require_once "_clases.php";
require_once "_dao.php";
require_once "_sesiones.php";
require_once "_utilidades.php";
garantizarSesion();

if (isset($_REQUEST['actualizarDireccion'])){
    DAO::clienteActualizarDireccion($_REQUEST['direccion']);
    redireccionar($_SERVER['HTTP_REFERER']); // volver a la pagina que me llamo
}

