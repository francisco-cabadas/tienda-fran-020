<?php
session_start();
session_destroy();
unset($_SESSION['sesionIniciada']);
unset($_SESSION['tipoUsuario']);
setcookie("CookieId", 0, time() - (60 * 60 * 24 * 365));
setcookie("CookieNumAleatorio", 0, time() - (60 * 60 * 24 * 365));
header("Location: inicio-sesion.php");

?>

