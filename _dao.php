<?php

// Prueba Alain

require_once "_clases.php";

class DAO
{
    private static $pdo = null;

    private static function obtenerPdoConexionBD()
    {
        $servidor = "localhost";
        $identificador = "root";
        $contrasenna = "";
        $bd = "tienda"; // Schema
        $opciones = [
            PDO::ATTR_EMULATE_PREPARES => false, // Modo emulación desactivado para prepared statements "reales"
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Que los errores salgan como excepciones.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // El modo de fetch que queremos por defecto.
        ];

        try {
            $pdo = new PDO("mysql:host=$servidor;dbname=$bd;charset=utf8", $identificador, $contrasenna, $opciones);
        } catch (Exception $e) {
            error_log("Error al conectar: " . $e->getMessage());
            exit("Error al conectar");
        }

        return $pdo;
    }

    private static function ejecutarConsulta(string $sql, array $parametros): array
    {
        if (!isset(self::$pdo)) self::$pdo = self::obtenerPdoConexionBd();

        $select = self::$pdo->prepare($sql);
        $select->execute($parametros);
        return $select->fetchAll();
    }

    private static function ejecutarActualizacion(string $sql, array $parametros): void
    {
        if (!isset(self::$pdo)) self::$pdo = self::obtenerPdoConexionBd();

        $actualizacion = self::$pdo->prepare($sql);
        $actualizacion->execute($parametros);
    }

    private static function crearClienteDesdeRs(array $rs): Cliente
    {
        return new Cliente($rs[0]["id"], $rs[0]["email"], $rs[0]["contrasenna"], $rs[0]["codigoCookie"], $rs[0]["nombre"], $rs[0]["direccion"], $rs[0]["telefono"]);
    }

    public static function clienteObtenerPorId(int $id): Cliente
    {
        $rs = self::ejecutarConsulta("SELECT * FROM cliente WHERE id=?", [$id]);
        return self::crearClienteDesdeRs($rs);
    }

    public static function clienteObtenerPorEmailYContrasenna($email, $contrasenna): Cliente
    {
        $rs = self::ejecutarConsulta("SELECT * FROM cliente WHERE BINARY email=? AND BINARY contrasenna=?", [$email, $contrasenna]);
        if ($rs) {
            return self::crearClienteDesdeRs($rs);
        } else {
            return null;
        }
    }

    public static function productoObtenerTodos(): array
    {
        $datos = [];
        $rs = self::ejecutarConsulta("SELECT * FROM producto ORDER BY nombre", []);

        foreach ($rs as $fila) {
            $producto = new producto($fila["id"], $fila["nombre"], $fila["descripcion"], $fila["precio"]);
            array_push($datos, $producto);
        }
        return $datos;
    }

    public static function productoObtenerPorId(int $id): Producto
    {
        $rs = self::ejecutarConsulta("SELECT * FROM producto WHERE id=?", [$id]);
        $producto = new Producto($rs[0]["id"], $rs[0]["nombre"], $rs[0]["descripcion"], $rs[0]["precio"]);
        return $producto;
    }
    public static function productoActualizar(int $id,string $nuevoNombre, string $nuevaDescripcion, int $nuevoPrecio){
        //revisar esta funcion, lo de [id] no me queda claro
        $rs=self::ejecutarActualizacion("UPDATE producto SET nombre = ?, descripcion = ?, precio =? WHERE id=?", [$nuevoNombre,$nuevaDescripcion,$nuevoPrecio,$id]);
    }

    private static function carritoCrearParaCliente(int $id): Carrito
    {
        self::ejecutarConsulta("INSERT INTO pedido (cliente_id, direccionEnvio, fechaConfirmacion) VALUES (?, NULL, NULL) ", [$id]);
        // TODO Parece que esto no actúa: hay que mirar qué pasa.
    }

    // Si no existe, se creará.
    public static function carritoObtenerParaCliente(int $id): Carrito
    {
        $arrayLineasParaCarrito = array();

        $rs = self::ejecutarConsulta("SELECT * FROM linea INNER JOIN pedido ON linea.pedido_id = pedido.id WHERE cliente_id=? AND fechaConfirmacion IS null ", [$id]);
        if (!$rs) {
            self::carritoCrearParaCliente($id);
            $rs = self::ejecutarConsulta("SELECT * FROM pedido WHERE cliente_id=? AND fechaConfirmacion IS null", [$id]);
            $carrito = new Carrito(
                $rs[0]['id'],
                null
            );

        }

        foreach ($rs as $fila){
            $linea= new LineaCarrito(
                $fila['producto_id'],
                $fila['unidades']
            );
            array_push($arrayLineasParaCarrito, $linea);
        }
        $carrito = new Carrito (
            $rs[0]['cliente_id'],
            $arrayLineasParaCarrito
        );

        return $carrito;
    }

    private static function carritoObtenerUnidadesProducto($clienteId, $productoId): int
    {
        $rs = self::ejecutarConsulta("SELECT unidades FROM linea INNER JOIN pedido on linea.pedido_id = pedido.id WHERE cliente_id=? AND producto_id=? ", [$clienteId, $productoId]);
        if(!$rs){
            return false;
        }else{
            return $rs[0]['unidades'];
        }
    }

    private static function carritoEstablecerUnidadesProducto($clienteId, $productoId, $nuevaCantidadUnidades, $pedidoId)
    {
        $unidadesIniciales = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        $unidadesDefinitivas=$unidadesIniciales+$nuevaCantidadUnidades;

        // TODO Hay que quitar los echo-s de aquí.
        if (!$unidadesIniciales && $nuevaCantidadUnidades >= 1) {
            echo("i");
            self::ejecutarConsulta("INSERT INTO linea (pedido_Id, producto_id, unidades, precioUnitario) VALUES (?, ?, ?, NULL )", [$pedidoId, $productoId, $unidadesDefinitivas]);
            // PrecioUnitario en vez de null-> $precioProducto*$nuevaCantidadUnidades
        } else if ($unidadesIniciales > 0 && $nuevaCantidadUnidades >= 1) {
            echo("u");
            self::ejecutarConsulta("UPDATE linea SET unidades=? WHERE pedido_id=? AND producto_id=?", [$unidadesDefinitivas, $pedidoId, $productoId]);
            // Habria que añadir al set el PrecioUnitario ($precioProducto * $nuevaCantidadUnidades)
        } else if ($unidadesIniciales>0 && $nuevaCantidadUnidades < 0) {
            echo("d");
            self::ejecutarConsulta("DELETE FROM linea WHERE pedido_id=? AND producto_id=?", [$pedidoId, $productoId]);
        } else { // Quieren quitar unidades de un prodcuto que no existe, informar al usuario de ello.
            echo("?");
        }

    }

    public static function carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades): int
    {
        $rs = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        $rsPedido= self::ejecutarConsulta("SELECT id FROM pedido WHERE cliente_id=? ", [$clienteId]);
        $pedidoId = $rsPedido[0]['id'];
        if (!$rs) {
            $nuevaCantidadUnidades = $variacionUnidades;
        } else {
            $nuevaCantidadUnidades = $variacionUnidades + $rs[0]['unidades'];
        }

        self::carritoEstablecerUnidadesProducto($clienteId, $productoId, $nuevaCantidadUnidades, $pedidoId);
        return $nuevaCantidadUnidades;
    }
}
