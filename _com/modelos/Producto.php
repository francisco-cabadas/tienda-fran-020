<?php
require_once "Dato.php";
require_once "Identificable.php";

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

    public function generarPrecioFormateado(): string
    {
        return number_format ($this->getPrecio(), 2) . "€";
    }
}
