<?php

require_once "../_com/comunes-app.php";

$carrito = DAO::carritoObtenerParaCliente($_SESSION["id"]);
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

  if ($carrito) {
      foreach ($carrito->getLineas() as $linea) {
          $producto = DAO::productoObtenerPorId($linea->getProductoId());
          $importeLinea = $linea->getUnidades() * $producto->getPrecio();
          $totalCarrito += $importeLinea;
          ?>
        <tr>
          <td>
            <a href='producto-detalle.php?id=<?= $producto->getId() ?>'><?= $producto->getNombre() ?></a>
          </td>
          <td class="text-center">
            <form action="carrito-gestionar-producto.php" method="post">
              <input type="hidden" name="cambiarCantidad" value="true">
              <input type="hidden" name="productoId" value="<?= $producto->getId() ?>">
              <input type="number" name="unidades" id="unidades" value="<?= $linea->getUnidades(); ?>">
              <input type="submit" value="Operar">
            </form>
          </td>
          <td class="text-center"><?= $producto->generarPrecioFormateado() ?></td>
          <td class="text-center"><?= $importeLinea ?>€</td>
          <td class="text-center"><a
                href="carrito-gestionar-producto.php?productoId=<?= $producto->getId() ?>&eliminar=true">X</a>
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
<?php
if($carrito) {
    if ($carrito->getLineas() != null) { ?>
        <a href="pedido-previsualizar.php">Confirmar Pedido</a>
    <?php }
}?>
<a href="productos-listado.php">Seguir Comprando</a>
<?php require "../_com/info-sesion.php"; ?>

</body>

</html>