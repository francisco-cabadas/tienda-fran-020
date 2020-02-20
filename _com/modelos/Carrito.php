<?php
require_once "ProtoPedido.php";

class Carrito extends ProtoPedido {

    public function __construct(int $cliente_id, $lineas)
    {
        parent::__construct($cliente_id, $lineas);
    }
    //TODO VER ESTO A VER  SI FUNCIONA
    public function variarProducto($productoId, $variacionUnidades) {
        $nuevaCantidadUnidades = DAO::carritoVariarUnidadesProducto($productoId, $variacionUnidades);

        $lineas = $this->getLineas();
        $lineaNueva= new LineaCarrito($productoId, $nuevaCantidadUnidades);
        array_push($lineas, $lineaNueva);
        $this->setLineas($lineas);
    }
}
