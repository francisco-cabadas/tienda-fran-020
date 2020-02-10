<?php
require_once "_clases.php";
require_once "_sesiones.php";
sessionStartSiNoLoEsta();


class DAO
{
    private static $pdo = null;

    private static function obtenerPdoConexionBD()
    {
        $servidor = "mysql";
        $identificador = "root";
        $contrasenna = "root";
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
            exit("Error al conectar" . $e->getMessage());
        }

        return $pdo;
    }

    private static function ejecutarConsulta(string $sql, array $parametros): array
    {
        if (!isset(self::$pdo)) {
            self::$pdo = self::obtenerPdoConexionBd();
        }

        $select = self::$pdo->prepare($sql);
        $select->execute($parametros);
        return $select->fetchAll();
    }

    private static function ejecutarAccion(string $sql, array $parametros): void
    {
        if (!isset(self::$pdo)) {
            self::$pdo = self::obtenerPdoConexionBd();
        }

        $actualizacion = self::$pdo->prepare($sql);
        $actualizacion->execute($parametros);
    }

    /* CLIENTE */

    private static function crearClienteDesdeRs(array $rs): Cliente
    {
        return new Cliente($rs[0]["id"], $rs[0]["email"], $rs[0]["contrasenna"], $rs[0]["codigoCookie"],
            $rs[0]["nombre"], $rs[0]["telefono"], $rs[0]["direccion"]);
    }

    public static function clienteObtenerPorId(int $id): Cliente
    {
        $rs = self::ejecutarConsulta("SELECT * FROM cliente WHERE id=?", [$id]);
        return self::crearClienteDesdeRs($rs);
    }

    public static function clienteObtenerPorEmailYContrasenna($email, $contrasenna): Cliente
    {
        $rs = self::ejecutarConsulta("SELECT * FROM cliente WHERE BINARY email=? AND BINARY contrasenna=?",
            [$email, $contrasenna]);
        if ($rs) {
            return self::crearClienteDesdeRs($rs);
        } else {
            return null;
        }
    }

    public static function clienteActualizarDireccion($direccion): void
    {
        self::ejecutarAccion(
            "UPDATE cliente SET direccion=? WHERE id=?",
            [$direccion, $_SESSION["id"]]
        );
    }

    /* PRODUCTO */

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

    public static function productoActualizar(int $id, string $nuevoNombre, string $nuevaDescripcion, int $nuevoPrecio)
    {
        //revisar esta funcion, lo de [id] no me queda claro
        $rs = self::ejecutarAccion("UPDATE producto SET nombre = ?, descripcion = ?, precio =? WHERE id=?",
            [$nuevoNombre, $nuevaDescripcion, $nuevoPrecio, $id]);
    }

    /* CARRITO */

    private static function carritoCrearParaCliente(int $id): void
    {
        self::ejecutarAccion("INSERT INTO pedido (cliente_id) VALUES (?) ", [$id]);
    }

    public static function carritoObtenerParaCliente(int $clienteId): Carrito
    {
        $arrayLineasParaCarrito = array();
        $pedidoId = self::pedidoObtenerId($clienteId);

        if (!$pedidoId) {
            self::carritoCrearParaCliente($clienteId);
            $pedidoId = self::pedidoObtenerId($clienteId);
            return new Carrito(
                $clienteId,
                $arrayLineasParaCarrito
            );
        }

        $rsLineas = self::ejecutarConsulta("SELECT * FROM linea WHERE pedido_id=?", [$pedidoId]);

        foreach ($rsLineas as $fila) {
            $linea = new LineaCarrito(
                $fila['producto_id'],
                $fila['unidades']
            );
            array_push($arrayLineasParaCarrito, $linea);
        }
        $carrito = new Carrito (
            $clienteId,
            $arrayLineasParaCarrito
        );

        return $carrito;
    }

    private static function carritoObtenerUnidadesProducto($pedidoId, $productoId): int
    {
        $rs = self::ejecutarConsulta("SELECT unidades FROM linea WHERE pedido_id=? AND producto_id=? ",
            [$pedidoId, $productoId]);
        if (!$rs) {
            return 0;
        } else {
            return $rs[0]['unidades'];
        }
    }

    public static function carritoEstablecerUnidadesProducto($productoId, $nuevaCantidad, $pedidoId): void
    {

        $udsIniciales = self::carritoObtenerUnidadesProducto($pedidoId, $productoId);
        if ($udsIniciales <= 0) {
            self::ejecutarAccion(
                "INSERT INTO linea (pedido_id, producto_id, unidades) VALUES (?,?,?)",
                [$pedidoId, $productoId, $nuevaCantidad]
            );
            exit();
        }

        if ($nuevaCantidad<=>0){

        }

        self::ejecutarAccion(
            "UPDATE linea SET unidades=? WHERE pedido_id=? AND producto_id=?",
            [$nuevaCantidad, $pedidoId, $productoId]
        );

    }

    public static function carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades): int
    {
        $rs = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        $rsPedido = self::ejecutarConsulta("SELECT id FROM pedido WHERE cliente_id=? ", [$clienteId]);
        $pedidoId = $rsPedido[0]['id'];
        if (!$rs) {
            $nuevaCantidadUnidades = $variacionUnidades;
        } else {
            $nuevaCantidadUnidades = $variacionUnidades + $rs[0]['unidades'];
        }

        self::carritoEstablecerUnidadesProducto($productoId, $nuevaCantidadUnidades, $pedidoId);
        return $nuevaCantidadUnidades;
    }

    public static function carritoAgregarProducto(int $clienteId, $productoId)
    {
        $pedidoId = self::pedidoObtenerId($clienteId);

        self::ejecutarAccion(
            "INSERT INTO linea (pedido_id, producto_id, unidades) VALUES (?,?,1)",
            [$pedidoId,$productoId]
        );
    }

    /* LINEA */

    public static function lineaEliminar($pedidoId, $productoId)
    {
        self::ejecutarAccion(
            "DELETE from linea WHERE pedido_id=? AND producto_id=?",
            [$pedidoId, $productoId]);
    }

    private static function lineaFijarPrecio($productoId, $pedidoId)
    {
        $precio = self::productoObtenerPorId($productoId)->getPrecio();
        self::ejecutarAccion(
            "UPDATE linea SET precioUnitario=? WHERE producto_id=? AND pedido_id=?",
            [$precio, $productoId, $pedidoId]
        );
    }

    /* PEDIDO */

    public static function pedidoObtenerId(int $clienteId)
    {
        $rsPedidoId = self::ejecutarConsulta(
            "SELECT id FROM pedido WHERE cliente_id=? AND fechaConfirmacion IS NULL",
            [$clienteId]
        );
        $pedidoID = $rsPedidoId[0]["id"];
        return $pedidoID;
    }

    public static function pedidoConfirmar(int $pedidoId)
    {
        $direccion = DAO::clienteObtenerPorId($_SESSION["id"])->getDireccion();
        $fechaAhora = obtenerFecha();
        self::pedidoFijarPrecios($pedidoId);
        self::ejecutarAccion(
            "UPDATE pedido SET fechaConfirmacion=?, direccionEnvio=? WHERE id=?",
            [$fechaAhora, $direccion, $pedidoId]
        );
    }

    private static function pedidoFijarPrecios(int $pedidoId)
    {
        $carrito = self::carritoObtenerParaCliente($_SESSION["id"]);
        foreach ($carrito->getLineas() as $linea){
            self::lineaFijarPrecio($linea->getProductoId(), $pedidoId);
        }
    }

}
