<?php
require_once "Dato.php";
require_once "Identificable.php";

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
