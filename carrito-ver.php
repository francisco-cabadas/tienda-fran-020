<?php

require_once "_sesiones.php";
require_once "_clases.php";
require_once "_dao.php";

garantizarSesion();

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
  /* $lineaP= new LineaCarrito(1,3);
   $lineaP2= new LineaCarrito(2,7);
   $lineasP[0]=$lineaP;
   $lineasP[1]=$lineaP2;
   $carritoP = new Carrito(1, $lineasP); */

  if ($carrito->getLineas() != null) {
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
              <input type="number" min="0" name="unidades" id="unidades" value="<?= $linea->getUnidades(); ?>">
              <input type="submit" value="Actualizar">
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
if ($carrito->getLineas() != null){ ?>
  <a href="pedido-previsualizar.php">Confirmar Pedido</a>
<?php } ?>
<a href="productos-listado.php">Seguir Comprando</a>
<?php require "_info-sesion.php"; ?>


</body>


</html>