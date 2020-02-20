<?php
require_once "ProtoLinea.php";

class LineaPedido extends ProtoLinea
{
    private  $precioUnitario;

    public function __construct(int $producto_id, int $unidades, float $precioUnitario)
    {
        parent::__construct($producto_id, $unidades);

        $this->setPrecioUnitario($precioUnitario);
    }

    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;
    }
}