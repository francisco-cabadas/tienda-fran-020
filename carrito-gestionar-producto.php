<?php

$clienteId = 1; //$_SESSION["id"];  Lo dejamos en 1 considerando que es el usuario "jlopez"
$productoId = $_REQUEST["productoId"];
$variacionUnidades = $_REQUEST["variacionUnidades"];

$carrito = DAO::carritoObtenerParaCliente($clienteId);

$unidadesAnteriores = $carrito->obtenerUnidadesProducto($productoId);
$unidadesDefinitivas = ...

if ($unidadesDefinitivas > 0) { // Si la variacion es mayor que 0 significa que queremos añadir un producto a nuestro carrito
    ... ?
    $lineaNueva = new LineaCarrito($productoId, $variacionUnidades); // Creamos un objeto lineaCarrito con el nuevo producto
    $arrayLineas = $carrito->getLineas(); // Recogemos el array de lineas de nuestro carrito para no machacar las anteriores
    array_push($arrayLineas, $lineaNueva); //Añadimos al array la nueva linea
    $carrito->setLineas($arrayLineas); // Seteamos el carrito con el array que incluye la nueva linea
    //Aqui hacemos constancia de la variacion en la base de datos para
    //posteriormente confirmar el carrito y convertirlo en un pedido.
    // esto no me inspira mucha confianza pues en lineaPedido y linea
    // el id del pedido no puede ser nulo...
    DAO::carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades);
} else { // En este caso entendemos que queremos borrar 'x' unidades de un producto
    if($variacionUnidades == -6335234){ //Como comentamos en clase, utilizamos un numero reservado para borrar todas las unidades de un producto del carrito

    }else{ //En este punto tendriamos que eliminar 'x' cantidades de un producto en un carrito

    }
}
?>