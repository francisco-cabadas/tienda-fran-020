<?php
	require_once "_utilidades.php";

	session_start();

	session_destroy();
	
	// No hace falta hacer unset($_SESSION), porque este PHP no tiene HTML.

	// Se redirige al cliente a otro PHP.
	redireccionar("iniciar-sesion.php?sesionCerrada=true");
?>