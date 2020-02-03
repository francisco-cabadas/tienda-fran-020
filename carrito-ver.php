<?php

require_once "_comprobar-sesion.php";
require_once "_clases.php";
require_once "_dao.php";
$carrito = DAO::obtenerCarritoPorCliente($_SESSION["id"]); //O clienteId o id o idCliente
$subTotal = 0;

?>

<html>

<head>
  <!-- Inyectamos las dependencias de bootstrap desde la plantilla -->
    <?php include("templates/bootstrap.php"); ?>
  <meta charset="UTF-8">
  <title>carrito ver</title>
</head>

<body>

<?php require "_info-sesion.php"; ?>

<h1>Tu carrito</h1>

<div class="container">
  <div class="w-75 mx-auto">
    <table class="table table-striped">
      <thead>
      <tr>
        <th style="width: 10%" scope="col">Cantidad</th>
        <th style="width: 40%" scope="col">Articulo</th>
        <th class="text-center" style="width: 20%" scope="col">Precio</th>
        <th class="text-center" style="width: 20%" scope="col">Total</th>
        <th class="text-center" style="width: 10%" scope="col">Eliminar</th>
      </tr>
      </thead>
      <tbody>
      <?php if ($carrito) {
          foreach ($productos as $fila) {
              $producto = DAO::obtenerProductoPorId($fila["producto_id"]);//obtenerProductoPorId
              $totalFila = $producto["precio"] * $fila["unidades"];
              $subTotal += $totalFila;
              ?>

            <tr>
              <td class="text-center"><?= $fila["unidades"] ?></td>
              <td><a href='producto-detalle.php?id=<?= $producto["id"] ?>'><?= $producto["nombre"] ?></a></td>
              <td class="text-center"><?= $producto["precio"] ?></td>
              <td class="text-center"><?= $totalFila ?></td>
              <td class="text-center"><a
                    href="carrito-gestionar-producto.php?productoId=<?= $fila['producto_id'] ?>&eliminar=true"><img
                      src="img/eliminar.png" alt="Eliminar"></a></td>
            </tr>
          <?php }
      } ?>
      </tbody>
    </table>
    <div class="d-flex justify-content-end">
      <table class="table w-50">
        <thead>
        <tr>
          <th style="width: 70%" scope="col"></th>
          <th style="width: 30%" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td>Subtotal:</td>
          <td><?= $subTotal ?> €</td>
        </tr>
        <tr>
          <td>Gastos de Envio:</td>
          <!--TODO: gestion gastos de envio-->
          <td></td>
        </tr>
        <tr>
          <td><strong>Total:</strong></td>
          <td><strong><?= $subTotal ?> €</strong></td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
