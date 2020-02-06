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

    public static function clienteObtenerPorEmailYContrasenna($email, $contrasenna)
    {
        $rs = self::ejecutarConsulta("SELECT * FROM cliente WHERE BINARY email=? AND BINARY contrasenna=?", [$email, $contrasenna]);
        if ($rs) {
            return new Cliente($rs[0]["id"], $rs[0]["email"], $rs[0]["contrasenna"], $rs[0]["codigoCookie"], $rs[0]["nombre"], $rs[0]["telefono"], $rs[0]["direccion"]);
        } else {
            return null;
        }
    }

    public static function productoObtenerTodos(): array
    {
        $datos = [];
        $rs = self::ejecutarConsulta("Select * from producto order by nombre", []);

        foreach ($rs as $fila) {
            $producto = new producto($fila["id"], $fila["nombre"], $fila["descripcion"], $fila["precio"]);
            array_push($datos, $producto);
        }
        return $datos;
    }

    public static function productoObtenerPorId(int $id)
    {
        $rs = self::ejecutarConsulta("select * from producto where id=?", [$id]);
        $producto = new Producto($rs[0]["id"], $rs[0]["nombre"], $rs[0]["descripcion"], $rs[0]["precio"]);
        return $producto;
    }

    private static function carritoCrearParaCliente(int $id)
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

    private static function carritoObtenerUnidadesProducto($clienteId, $productoId) :int
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
        }else{ // Quieren quitar unidades de un prodcuto que no existe, informar al usuario de ello.
            echo("?");
        }

    }

    public static function carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades)
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

    public static function inicioSesion()
    {
        $correcto=false;
        if (isset($_SESSION['sesionIniciada'])) {
            $correcto = true;

        } else if (!isset($_SESSION['sesionIniciada']) && isset($_REQUEST['contrasenna']) && isset($_REQUEST['email'])) {

            $rs=self::ejecutarConsulta("select * from cliente where email =? and contrasenna =?",[$_REQUEST['email'], $_REQUEST['contrasenna']]);
            if ($rs) {
                $correcto = true;

                $idUsuario = $rs[0]["id"];
                $_SESSION['sesionIniciada'] = true;
                $_SESSION["identificador"] = $idUsuario;

                if (isset($_REQUEST["guardar_clave"]) && $_REQUEST["guardar_clave"] == "1") {
                    mt_srand(time());
                    $numero_aleatorio = mt_rand(1000000, 999999999);
                    self::ejecutarConsulta("update cliente set codigoCookie=? where id=?",[$numero_aleatorio, $idUsuario]);
                    setcookie("CookieId", $idUsuario, time() + (60 * 60 * 24 * 365));
                    setcookie("CookieNumAleatorio", $numero_aleatorio, time() + (60 * 60 * 24 * 365));
                }
            } else {
                setcookie('incorrecto', true, time() + 60 * 60);

                $correcto = false;
            }
        } else if (!isset($_SESSION['sesionIniciada']) && isset($_COOKIE["CookieId"]) && isset($_COOKIE["CookieNumAleatorio"])) {
            $rs=self::ejecutarConsulta("select * from cliente where id=? and codigoCookie=?",[$_COOKIE["CookieId"], $_COOKIE["CookieNumAleatorio"]]);
            if ($rs) {
                $_SESSION['sesionIniciada'] = true;
                $_SESSION["identificador"] = $_COOKIE["CookieId"];
                $correcto = true;
            } else {
                setcookie("CookieId", 0, time() - 3600);
                setcookie("CookieNumAleatorio", 0, time() - 3600);

            }

        } else {

            $correcto = false;
        }
        return $correcto;

    }



    }
    public static function clienteObtenerPorId(int $id)
    {
        $rs = self::ejecutarConsulta("select * from cliente where id=?", [$id]);
        $cliente = new Cliente($rs[0]["id"], $rs[0]["email"], $rs[0]["contrasenna"], $rs[0]["codigoCookie"], $rs[0]["nombre"], $rs[0]["direccion"], $rs[0]["telefono"]);
        return $cliente;
    }


}
