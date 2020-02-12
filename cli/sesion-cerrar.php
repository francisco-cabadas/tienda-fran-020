<?php

require_once "../_com/comunes-app.php";

// TODO Poner función destruirSesion() en sesiones.php y llamarle desde aquí.

session_destroy();
unset($_SESSION['sesionIniciada']);
unset($_SESSION['tipoUsuario']);
setcookie("CookieId", 0, time() - (60 * 60 * 24 * 365));
setcookie("CookieNumAleatorio", 0, time() - (60 * 60 * 24 * 365));
redireccionar("sesion-inicio.php");

?>

