<?php
require_once "ProtoLinea.php";


class LineaCarrito extends ProtoLinea
{
    public function __construct(int $producto_id, int $unidades)
    {
        parent::__construct($producto_id, $unidades);
    }
}