<?php

abstract class Dato
{
}

trait Identificable
{
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
}

class Cliente extends Dato {
    use Identificable;

    private  $email;
    private  $contrasenna;
    private  $codigoCookie;
    private  $nombre;
    private  $telefono;
    private  $direccion;

    public function __construct($id, $email, $contrasenna, $codigoCookie, $nombre, $telefono, $direccion)
    {
        $this->setId($id);
        $this->setEmail($email);
        $this->setContrasenna($contrasenna);
        $this->setCodigoCookie($codigoCookie);
        $this->setNombre($nombre);
        $this->setTelefono($telefono);
        $this->setDireccion($direccion);

    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getContrasenna()
    {
        return $this->contrasenna;
    }

    public function setContrasenna($contrasenna)
    {
        $this->contrasenna = $contrasenna;
    }

    public function getCodigoCookie()
    {
        return $this->codigoCookie;
    }

    public function setCodigoCookie($codigoCookie)
    {
        $this->codigoCookie = $codigoCookie;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }
}

class Producto extends Dato
{
    use Identificable;

    private $nombre;
    private $descripcion;
    private $precio;

    function __construct(int $id=null, string $nombre, string $descripcion, float $precio)
    {
        if        ($id != null && $nombre == null) { // Cargar de BD
            // TODO obtener info de la BD usando el id.
        } else if ($id == null && $nombre != null) { // Crear en BD
           DAO::agregarProducto($nombre,$descripcion,$precio);
        } else { // No hacemos nada con la BD (debe venir todo relleno)
            $this->id = $id;
            $this->nombre = $nombre;
            $this->descripcion = $descripcion;
            $this->precio = $precio;
        }
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
    }

    //añadida clase para producto-detalle que actualiza el objeto producto
    public function actualizarProducto(int $id, string $nombre, string $descripcion, int $precio): void
    {
        DAO::productoActualizar($id,$nombre,$descripcion,$precio);
        $producto = DAO::productoObtenerPorId($id);
        $producto->setNombre($nombre) ;
        $producto->setDescripcion ($descripcion);
        $producto->setPrecio($precio);
    }
    public function generarPrecioFormateado(): string
    {
        return number_format ($this->getPrecio(), 2) . "€";
    }
}

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
     public function addLinea($linea){
        if ($linea instanceof ProtoLinea) {
            array_push($this->lineas, $linea);
        }
    }

    public function variarProducto($productoId, $variacionUnidades) {
        $nuevaCantidadUnidades = DAO::carritoVariarUnidadesProducto($this->getClienteId(),$productoId, $variacionUnidades);

        $lineas = $this->getLineas();
        $lineaNueva= new LineaCarrito($productoId, $nuevaCantidadUnidades);
        array_push($lineas, $lineaNueva);
        $this->setLineas($lineas);
    }
}

class Carrito extends ProtoPedido {

    public function __construct(int $cliente_id, $lineas)
    {
        parent::__construct($cliente_id, $lineas);
    }
}

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

abstract class ProtoLinea
{
    protected $producto_id;
    protected $unidades;


    public function __construct(int $producto_id, int $unidades)
    {
        $this->producto_id = $producto_id;
        $this->unidades = $unidades;
    }


    public function getProductoId() : int
    {
        return $this->producto_id;
    }


    public function setProductoId($producto_id): void
    {
        $this->producto_id = $producto_id;
    }

    public function getUnidades()
    {
        return $this->unidades;
    }


    public function setUnidades($unidades): void
    {
        $this->unidades = $unidades;
    }




}

class LineaCarrito extends ProtoLinea
{
    public function __construct(int $producto_id, int $unidades)
    {
        parent::__construct($producto_id, $unidades);
    }
}

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