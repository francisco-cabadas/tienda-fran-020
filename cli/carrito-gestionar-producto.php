<?php

require_once "../_com/comunes-app.php";

if (isset($_REQUEST['agregar']))
{
    $carrito = DAO::carritoObtenerParaCliente($_SESSION["id"]);
    foreach ($carrito->getLineas() as $linea){
        if ($linea->getProductoId() == $_REQUEST['productoId']){
            DAO::carritoEstablecerUnidadesProducto(
                intval($_REQUEST['productoId']),
                $linea->getUnidades() + 1,
                $carrito->getId()
            );
            redireccionar("productos-listado.php");
        }
    }
    DAO::carritoAgregarProducto($_SESSION["id"],$_REQUEST['productoId']);
    redireccionar("productos-listado.php");
}

if (isset($_REQUEST['eliminar']))
{
    $pedidoId = DAO::pedidoObtenerId($_SESSION["id"]);
    DAO::lineaEliminar($pedidoId,$_REQUEST['productoId']);
    redireccionar("carrito-ver.php");
}

if (isset($_REQUEST['cambiarCantidad']))
{
    $pedidoId = DAO::pedidoObtenerId($_SESSION["id"]);
    DAO::carritoEstablecerUnidadesProducto(
        intval($_REQUEST['productoId']),
        intval($_REQUEST['unidades']),
        $pedidoId
    );
    redireccionar("carrito-ver.php");
}