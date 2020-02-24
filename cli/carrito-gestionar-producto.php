<?php

require_once "../_com/comunes-app.php";


// lo utiliza pruducto-listado para añadir 1 unidad al carrito cuando le damos a comprar
if (isset($_REQUEST['agregar']))
{
    $carrito = DAO::carritoObtenerParaCliente($_SESSION["id"]);
    $variacionUnidades=1;
    if ($_REQUEST["variacionUnidades"])
    {
        $variacionUnidades = $_REQUEST["variacionUnidades"];
    }
    if (!$carrito)
    {
        $carrito = DAO::carritoCrearParaCliente($_SESSION["id"]);
    }
    foreach ($carrito->getLineas() as $linea)
    {
        if ($linea->getProductoId() == $_REQUEST['productoId'])
        {
            DAO::carritoVariarUnidadesProducto($_SESSION["id"], $_REQUEST['productoId'], $variacionUnidades);
            redireccionar("productos-listado.php");
        }
    }
    DAO::carritoAgregarProducto($_SESSION["id"],$_REQUEST['productoId'], $variacionUnidades);
    redireccionar("productos-listado.php");
}

if (isset($_REQUEST['eliminar']))
{
    $pedidoId = DAO::carritoVariarUnidadesProducto($_SESSION["id"], $_REQUEST["productoId"], 0);
    redireccionar("carrito-ver.php");
}

if (isset($_REQUEST['cambiarCantidad']))
{
    DAO::carritoVariarUnidadesProducto($_SESSION["id"], $_REQUEST['productoId'], $_REQUEST['unidades']);
    redireccionar("carrito-ver.php");
}