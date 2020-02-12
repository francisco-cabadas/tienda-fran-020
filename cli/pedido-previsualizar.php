<?php

require_once "../_com/comunes-app.php";

$carrito = DAO::carritoObtenerParaCliente($_SESSION["id"]);
$totalCarrito = 0;
$cliente = DAO::clienteObtenerPorId($_SESSION["id"]);
$direccion = "";

if ($cliente->getDireccion() != null){
    $direccion = $cliente->getDireccion();
}else{
    $direccion= "No tiene dirección peredeterminada, especifíquela abajo.";
}

?>


<html>

<head>
    <meta charset="UTF-8">
    <title>Mi Pedido</title>
</head>

<body>

<h1>Mi Pedido</h1>

<table border="1">
    <thead>
    <tr>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>

    <?php
    if ($carrito) {
        foreach ($carrito->getLineas() as $linea) {

            $producto = DAO::productoObtenerPorId($linea->getProductoId());
            $importeLinea = $linea->getUnidades() * $producto->getPrecio();
            $totalCarrito += $importeLinea;
            ?>

            <tr>
                <td>
                    <?= $producto->getNombre() ?>
                </td>
                <td class="text-center">
                    <?= $linea->getUnidades(); ?>
                </td>
                <td class="text-center"><?= $producto->generarPrecioFormateado() ?></td>
                <td class="text-center"><?= $importeLinea ?>€</td>
            </tr>

            <?php
        }
    }
    ?>

    <tr>
        <td>Total del pedido:</td>
        <td><?= $totalCarrito ?> €</td>
    </tr>
    </tbody>
</table>
<h3>Dirección de envío</h3>

<p>Tu dirección predeterminada es: <?= $direccion?></p>

<form action="cliente-detalle-guardar.php" method="get">
    <!--<input type="hidden" name="actualizarDireccion" value="true">-->
    <input type="text" name="direccion"  placeholder="Inserte otra dirección de envío" style="WIDTH: 200px">
    <br>
    <input type="checkbox" name="sobreescribirDireccion">
    <label for "sobreescribirDireccion">Usar esta dirección como predeterminada</label>
    <br>
    <br>
    <input type="submit" name="confirmarPedido" value="Confirmar pedido">

</form>

<!--<button><a href="pedido-crear.php?confirmado=true">Finalizar Compra</a></button>-->

<br>
<br>

<a href="productos-listado.php">Seguir Comprando</a>

<?php require "../_com/info-sesion.php"; ?>


</body>


</html>
