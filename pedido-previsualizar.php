<?php

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();

$carrito = DAO::carritoObtenerParaCliente($_SESSION["id"]);
$totalCarrito = 0;
$cliente = DAO::clienteObtenerPorId($_SESSION["id"]);
$direccion = "";
if ($cliente->getDireccion() != null){
    $direccion = $cliente->getDireccion();
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
<form action="cliente-gestionar.php" method="get">
    <input type="hidden" name="actualizarDireccion" value="true">
    <input type="text" name="direccion" value="<?= $direccion?>" required>
    <input type="submit" value="Actualizar">
</form>
<a href="pedido-crear.php?confirmado=true">Finalizar Compra</a>
<a href="productos-listado.php">Seguir Comprando</a>
<?php require "_info-sesion.php"; ?>


</body>


</html>
