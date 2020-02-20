<?php

abstract class ProtoLinea
{
    protected $producto_id;
    protected $unidades;

    public function __construct(int $producto_id, int $unidades)
    {
        $this->producto_id = $producto_id;
        $this->unidades = $unidades;
    }

    public function getProductoId()
    {
        return $this->producto_id;
    }

    public function setProductoId($producto_id)
    {
        $this->producto_id = $producto_id;
    }

    public function getUnidades()
    {
        return $this->unidades;
    }

    public function setUnidades($unidades)
    {
        $this->unidades = $unidades;
    }
}
