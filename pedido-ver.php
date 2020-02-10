<?php

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();


$clienteId = $_REQUEST["id"];
$pedidos = DAO::pedidosObtenerTodosPorCliente($clienteId);



?>