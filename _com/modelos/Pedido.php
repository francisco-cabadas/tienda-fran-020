<?php
require_once "ProtoPedido.php";
require_once "Identificable.php";

class Pedido extends ProtoPedido {
    use Identificable;

    private  $direccionEnvio;
    private  $fechaConfirmacion; // $now = date("Y-m-d H:i:s"); tendriamos en la variable 2020-09-01 11:48 y es compatible con datetime de mysql

    public function __constructPedido(int $id, int $cliente_id, string $direccionEnvio, object $fechaConfirmacion, array $lineas)
    {
        parent::__construct($cliente_id, $lineas);

        $this->setId($id);
        $this->setDireccionEnvio($direccionEnvio);
        $this->getFechaConfirmacion($fechaConfirmacion);
    }

    public function getDireccionEnvio()
    {
        return $this->direccionEnvio;
    }

    public function setDireccionEnvio($direccionEnvio)
    {
        $this->direccionEnvio = $direccionEnvio;
    }

    public function getFechaConfirmacion()
    {
        return $this->fechaConfirmacion;
    }

    public function setFechaConfirmacion($fechaConfirmacion)
    {
        $this->fechaConfirmacion = $fechaConfirmacion;
    }
}
