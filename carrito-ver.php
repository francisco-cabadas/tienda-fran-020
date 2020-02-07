<?php

// TODO Redirige a iniciar sesión y no sabemos por qué.

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();

$carrito = DAO::carritoObtenerParaCliente(1); // TODO incorporar sesiones para obtener el cliente de $_SESSION["id"] //O clienteId o id o idCliente
$totalCarrito = 0;

?>


<html>

<head>
    <meta charset="UTF-8">
    <title>Ver mi carrito</title>
</head>

<body>

<h1>Mi carrito</h1>

<table border="1">
    <thead>
    <tr>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Total</th>
        <th>Eliminar</th>
    </tr>
    </thead>
    <tbody>

    <?php
        $lineaP= new LineaCarrito(1,3);
        $lineaP2= new LineaCarrito(2,7);
        $lineasP[0]=$lineaP;
        $lineasP[1]=$lineaP2;
        $carritoP = new Carrito(1, $lineasP);

    if ($carritoP) {

        foreach ($carritoP as $linea) {
            $producto = DAO::productoObtenerPorId($linea->parent::getProductoId());
            
            $importeLinea = $linea->parent::getUnidades() * $producto->getPrecio();
            $totalCarrito += $importeLinea;
            ?>

            <tr>
                <td><a href='producto-detalle.php?id=<?= $producto->getId() ?>'><?= $producto->getNombre() ?></a>
                </td>
                <td class="text-center"><?= $linea->parent::getUnidades() ?></td>
                <td class="text-center"><?= $producto->generarPrecioFormateado() ?></td>
                <td class="text-center"><?= $importeLinea ?></td>
                <td class="text-center"><a
                            href="carrito-gestionar-producto.php?productoId=<?= $producto->getId() ?>&variacionUnidades=eliminar">X</a>
                </td>
            </tr>

            <?php
        }
    }
    ?>

    <tr>
        <td>Total del carrito:</td>
        <td><?= $totalCarrito ?> €</td>
    </tr>
    </tbody>
</table>

</body>

<?php require "_info-sesion.php"; ?>

</html>