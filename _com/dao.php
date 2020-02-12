<?php

require_once "clases.php";
require_once "utilidades.php";

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

    public static function ejecutarActualizacion(string $sql, array $parametros): void
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
            return new Cliente($rs[0]["id"], $rs[0]["email"], $rs[0]["contrasenna"], $rs[0]["codigoCookie"], $rs[0]["nombre"], $rs[0]["telefono"], $rs[0]["direccion"]);
        } else {
            return null;
        }
    }

    public static function clienteGuardarCodigoCookie(string $email, string $codigoCookie)
    {
        if ($codigoCookie != null)
        {
            self::ejecutarActualizacion("UPDATE cliente SET codigoCookie=? WHERE email=?", [$codigoCookie, $email]);
        } else {
            self::ejecutarActualizacion("UPDATE cliente SET codigoCookie=NULL WHERE email=?", [$email]);
        }

    }

    public static function clienteActualizarDireccion($direccion): void
    {
        self::ejecutarActualizacion(
            "UPDATE cliente SET direccion=? WHERE id=?",
            [$direccion, $_SESSION["id"]]
        );
    }



    /* PRODUCTO */
    public static function agregarProducto($nombre,$descripcion,$precio){
        $rs = self::ejecutarActualizacion("INSERT INTO producto (id, nombre, descripcion, precio) VALUES (NULL, ?, ?, ?);", [$nombre,$descripcion,$precio]);
    }
    public static function productoObtenerTodos(): array
    {
        $datos = [];
        $rs = self::ejecutarConsulta("SELECT * FROM producto ORDER BY nombre", []);

        foreach ($rs as $fila) {
            $producto = new Producto($fila["id"], $fila["nombre"], $fila["descripcion"], $fila["precio"]);
            array_push($datos, $producto);
        }
        return $datos;
    }

    public static function productoObtenerPorId(int $id)
    {
        $rs = self::ejecutarConsulta("SELECT * FROM producto WHERE id=?", [$id]);
        $producto = new Producto($rs[0]["id"], $rs[0]["nombre"], $rs[0]["descripcion"], $rs[0]["precio"]);
        return $producto;
    }

    public static function productoActualizar(int $id, string $nuevoNombre, string $nuevaDescripcion, int $nuevoPrecio)
    {
        //revisar esta funcion, lo de [id] no me queda claro
        $rs = self::ejecutarActualizacion("UPDATE producto SET nombre = ?, descripcion = ?, precio =? WHERE id=?",
            [$nuevoNombre, $nuevaDescripcion, $nuevoPrecio, $id]);
    }



    /* CARRITO */

    public static function carritoCrearParaCliente(int $clienteId): Carrito
    {
        self::ejecutarActualizacion("INSERT INTO pedido (cliente_id) VALUES (?) ", [$clienteId]);
        $carrito= new Carrito($clienteId, []);
        return $carrito;
    }

    public static function carritoObtenerId(int $clienteId): int
    {
        $rsPedidoId = self::ejecutarConsulta(
            "SELECT id FROM pedido WHERE cliente_id=? AND fechaConfirmacion IS NULL",
            [$clienteId]
        );
        $pedidoID = $rsPedidoId[0]["id"];
        return $pedidoID;
    }

    // Si no existe, se creará.
    public static function carritoObtenerParaCliente(int $clienteId)
    {
        $arrayLineasParaCarrito = array();

        $rs = self::ejecutarConsulta("SELECT * FROM linea INNER JOIN pedido ON linea.pedido_id = pedido.id WHERE cliente_id=? AND fechaConfirmacion IS null", [$clienteId]);
        if (!$rs) {
            return null;
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
/*    public static function carritoObtenerParaCliente(int $clienteId): Carrito
    {
        $pedidoId = self::carritoObtenerPedidoIdDelCarritoParaCliente($clienteId);

        if (!$pedidoId) {
            self::carritoCrearParaCliente($clienteId);
            $pedidoId = self::carritoObtenerPedidoIdDelCarritoParaCliente($clienteId);
            return new Carrito(
                $clienteId,
                []
            );
        }
        $rsLineas = self::ejecutarConsulta("SELECT * FROM linea WHERE pedido_id=?", [$pedidoId]);

        $arrayLineasParaCarrito = [];
        foreach ($rsLineas as $fila) {
            $linea = new LineaCarrito(
                (int) $fila['producto_id'],
                (int) $fila['unidades']
            );
            array_push($arrayLineasParaCarrito, $linea);
        }
        $carrito = new Carrito (
            $clienteId,
            $arrayLineasParaCarrito
        );

        return $carrito;
    }
*/
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
//TODO REVISAR, CREO QUE IRÍA BIEN ASI
    public static function carritoEstablecerUnidadesProducto($productoId, $nuevaCantidad, $pedidoId): void
    {
        $udsIniciales = self::carritoObtenerUnidadesProducto($pedidoId, $productoId);
        if ($udsIniciales <= 0) {
            self::ejecutarActualizacion(
                "INSERT INTO linea (pedido_id, producto_id, unidades) VALUES (?,?,?)",
                [$pedidoId, $productoId, $nuevaCantidad]
            );
        }
        else if ($nuevaCantidad<=0){
            self::lineaEliminar($pedidoId, $productoId);
        }
        else {
            self::ejecutarActualizacion(
                "UPDATE linea SET unidades=? WHERE pedido_id=? AND producto_id=?",
                [$nuevaCantidad, $pedidoId, $productoId]
            );
        }
    }

    public static function carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades): int
    {
        $rsPedido = self::ejecutarConsulta("SELECT id FROM pedido WHERE cliente_id=? AND fechaConfirmacion IS null", [$clienteId]);
        $pedidoId = $rsPedido[0]['id'];
        $unidades = self::carritoObtenerUnidadesProducto($pedidoId, $productoId);
        if ($unidades==0) {
            $nuevaCantidadUnidades = $variacionUnidades;
        } else {
            $nuevaCantidadUnidades = $variacionUnidades + $unidades;
        }
        if ($variacionUnidades==0){
            self::carritoEstablecerUnidadesProducto($productoId, $variacionUnidades, $pedidoId);
            return $variacionUnidades;
        }
        else {
            self::carritoEstablecerUnidadesProducto($productoId, $nuevaCantidadUnidades, $pedidoId);
            return $nuevaCantidadUnidades;
        }
    }

    public static function carritoAgregarProducto(int $clienteId, $productoId, $unidades): void
    {
        $pedidoId = self::carritoObtenerId($clienteId);

        self::ejecutarActualizacion(
            "INSERT INTO linea (pedido_id, producto_id, unidades) VALUES (?,?,?) ",
            [$pedidoId,$productoId, $unidades]
        );
    }



    /* LINEA */

    public static function lineaEliminar($pedidoId, $productoId)
    {
        self::ejecutarActualizacion(
            "DELETE from linea WHERE pedido_id=? AND producto_id=?",
            [$pedidoId, $productoId]);
    }

    private static function lineaFijarPrecio($productoId, $pedidoId)
    {
        $precio = self::productoObtenerPorId($productoId)->getPrecio();
        self::ejecutarActualizacion(
            "UPDATE linea SET precioUnitario=? WHERE producto_id=? AND pedido_id=?",
            [$precio, $productoId, $pedidoId]
        );
    }



    /* PEDIDO */

    public static function pedidoConfirmar(int $pedidoId)
    {
        $direccion = DAO::clienteObtenerPorId($_SESSION["id"])->getDireccion();
        $fechaAhora = obtenerFecha();
        $codigoPedido = generarCadenaAleatoria(8);
        self::pedidoFijarPrecios($pedidoId);
        self::ejecutarActualizacion(
            "UPDATE pedido SET fechaConfirmacion=?, direccionEnvio=?, codigo_pedido = ? WHERE id=?",
            [$fechaAhora, $direccion, $pedidoId, $codigoPedido]
        );
    }

    private static function pedidoFijarPrecios(int $pedidoId)
    {
        $carrito = self::carritoObtenerParaCliente($_SESSION["id"]);
        foreach ($carrito->getLineas() as $linea){
            self::lineaFijarPrecio($linea->getProductoId(), $pedidoId);
        }
    }

    public static function pedidosObtenerTodosPorCliente($clienteId): array
    {
        $rsPedidos= self::ejecutarConsulta("SELECT pedido.id, pedido.direccionEnvio, pedido.fechaConfirmacion, pedido.codigo_pedido FROM pedido, cliente WHERE pedido.cliente_id=cliente.id AND pedido.cliente_id=? AND pedido.fechaConfirmacion IS NOT NULL", [$clienteId]);
        return $rsPedidos;

    }

    public static function pedidoObtenerProductos($pedidoId): array
    {
        $rs = self::ejecutarConsulta("SELECT linea.pedido_id, producto.nombre, linea.unidades, producto.precio FROM pedido, linea, producto WHERE pedido.id=linea.pedido_id AND producto.id=linea.producto_id AND pedido.id=?", [$pedidoId]);
        return $rs;
    }
}