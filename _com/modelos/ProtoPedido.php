<?php
require_once "Dato.php";

abstract class ProtoPedido extends Dato
{

    protected $cliente_id;
    protected $lineas;

    public function __construct(int $cliente_id, $lineas)
    {
        $this->cliente_id = $cliente_id;
        $this->lineas = $lineas;
    }

    public function getClienteId(): int
    {
        return $this->cliente_id;
    }

    public function setClienteId(int $cliente_id)
    {
        $this->cliente_id = $cliente_id;
    }

    public function getLineas(): array
    {
        return $this->lineas;
    }

    public function setLineas(array $lineas): void
    {
        $this->lineas = $lineas;
    }


}
