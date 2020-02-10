<?php

// Prueba Alain

require_once "_clases.php";
require_once "_utilidades.php";

class DAO
{
     static $pdo = null;

     static function obtenerPdoConexionBD()
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

        $rs = self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion IS null ", [$id]);
        if (!$rs) {
            self::carritoCrearParaCliente($id);
            $rs = self::ejecutarConsulta("select * from pedido where cliente_id=? AND fechaConfirmacion IS null ", [$id]);

        }

        $carrito = new Carrito (
            $rs[0]['id'],
            $rs[0]['cliente_id'],
            $rs[0]['direccionEnvio'],
            $rs[0]['fechaConfirmacion']
        );

        return $carrito;
    }

    private static function carritoObtenerUnidadesProducto($clienteId, $productoId, $pedidoId)
    {
        $rs = self::ejecutarConsulta("SELECT unidades FROM linea, pedido WHERE pedido.id=linea.pedido_id AND cliente_id=? AND producto_id=? ", [$clienteId, $productoId]);
        return $rs; // TODO Hay que devolver un int con las unidades, no un $rs: ... [0]["unidades"]
    }

    private static function carritoEstablecerUnidadesProducto($clienteId, $productoId, $nuevaCantidadUnidades, $pedidoId)
    {
        $unidadesIniciales = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        $unidadesDefinitivas=$unidadesIniciales+$nuevaCantidadUnidades;

        
        if (!$unidadesIniciales && $nuevaCantidadUnidades >= 1) {

            self::ejecutarConsulta("INSERT INTO linea (pedido_Id, producto_id, unidades, precioUnitario) VALUES (?, ?, ?, NULL )", [$pedidoId, $productoId, $unidadesDefinitivas]);
            // PrecioUnitario en vez de null-> $precioProducto*$nuevaCantidadUnidades
        } else if ($unidadesIniciales > 0 && $nuevaCantidadUnidades >= 1) {

            self::ejecutarConsulta("UPDATE linea SET unidades=? WHERE pedido_id=? AND producto_id=?", [$unidadesDefinitivas, $pedidoId, $productoId]);
            // Habria que añadir al set el PrecioUnitario ($precioProducto * $nuevaCantidadUnidades)
        } else if ($unidadesIniciales>0 && $nuevaCantidadUnidades < 0) {

            self::ejecutarConsulta("DELETE FROM linea WHERE pedido_id=? AND producto_id=?", [$pedidoId, $productoId]);
        } else { // Quieren quitar unidades de un prodcuto que no existe, informar al usuario de ello.

        }
        /* else { // Quieren quitar unidades de un prodcuto que no existe, informar al usuario de ello.
         */
    }

    public static function carritoVariarUnidadesProducto($clienteId, $productoId, $variacionUnidades)
    {
        $rs = self::carritoObtenerUnidadesProducto($clienteId, $productoId);
        $pedidoId = $rs[0]['pedido_id'];
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

    // TODO ¿Es necesario este método?
    public static function obtenerCantidadTotalProductoEnCarrito($productoId, $clienteId) : int{
        $rs = self::ejecutarConsulta("select id from pedido where id_cliente=?", [$clienteId]);
        $pedidoId= $rs['id'];

        $rsUnidadesProductos = self::ejecutarConsulta("select lineaPedido.unidades from lineaPedido where pedido_id=? and producto_id=?",
                                                        [$pedidoId, $productoId]);
        if (!$rsUnidadesProductos){
            return 0;
        }
        return $rsUnidadesProductos['unidades'];

    }
    






 

    


function borrarCookieRecuerdame($identificador)
{
    // TODO convertir a nuevo modelo DAO...

    // Eliminamos el código cookie de nuestra BD.
    $pdo = self::obtenerPdoConexionBd();
    $sql = "UPDATE usuario SET codigoCookie=NULL WHERE identificador=?";
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([$identificador]);

    setcookie("email", "", time() - 3600); // Tiempo en el pasado, para (pedir) borrar la cookie.
    setcookie("codigoCookie", "", time() - 3600); // Tiempo en el pasado, para (pedir) borrar la cookie.
}




}
function haySesionIniciada()
{
    return isset($_SESSION['sesionIniciada']);
}

    function garantizarSesion()
{
    if (haySesionIniciada()) {
        // Si hay cookie de "recuérdame", la renovamos.
        if (isset($_COOKIE["email"])) {
            establecerCookieRecuerdame($_COOKIE["email"], $_COOKIE["codigoCookie"]);
        }

        // >>> NO HACEMOS NADA MÁS. DEJAMOS QUE SE CONTINÚE EJECUTANDO EL PHP QUE NOS LLAMÓ... >>>
    } else { // NO hay sesión iniciada.
        if (vieneCookieRecuerdame()) {
            $email = $_COOKIE["email"];
            $codigoCookie = $_COOKIE["codigoCookie"];

            // Comprobaremos la información contra la BD.
            $cliente = DAO::____($email, $codigoCookie); // TODO Hacer esto con DAO.

            if ($cliente) { // Si viene un cliente es que existe el cliente y coincide el código cookie. Daremos por iniciada la sesión.
                // Recuperar los datos adicionales del usuario que acaba de iniciar sesión.
                anotarDatosSesionRam($cliente);

                // Renovar la cookie (código y caducidad).
                generarCookieRecuerdame($email);
            } else { // Parecía que venía una cookie válida pero... No es válida o pasa algo raro.
                // Borrar la cookie mala que nos están enviando (si no, la enviarán otra vez, y otra, y otra...)
                borrarCookieRecuerdame($email);

                // REDIRIGIR A INICIAR SESIÓN PARA IMPEDIR QUE ESTE USUARIO VISUALICE CONTENIDO PRIVADO.
                redireccionar("inicio-sesion.php");
            }
        } else if (vieneFormularioDeInicioDeSesion()) { // SÍ hay formulario enviado. Lo comprobaremos contra la BD.
            $cliente = DAO::clienteObtenerPorEmailYContrasenna($_REQUEST['email'], $_REQUEST['contrasenna']);

            if ($cliente) { // Si viene un cliente es que el inicio de sesión ha sido exitoso.
                anotarDatosSesionRam($cliente);

                if (isset($_REQUEST["recuerdame"])) { // Si han marcado el checkbox de recordar:
                    generarCookieRecuerdame($cliente->getEmail());
                }
                // >>> Y DEJAMOS QUE SE CONTINÚE EJECUTANDO EL PHP QUE NOS LLAMÓ... >>>
            } else { // Si vienen 0 filas, no existe ese usuario o la contraseña no coincide.
                redireccionar("inicio-sesion.php?incorrecto=true");
            }
        } else { // NO hay ni sesión, ni cookie, ni formulario enviado.
            // REDIRIGIMOS PARA QUE NO SE VISUALICE CONTENIDO PRIVADO:
            redireccionar("inicio-sesion.php");
        }
    }
}
function vieneCookieRecuerdame()
{
    return isset($_COOKIE["identificador"]);
}

// Comprueba si hay sesión-usuario iniciada en la sesión-RAM.


function vieneFormularioDeInicioDeSesion()
{
    return isset($_REQUEST['email']);
}

function anotarDatosSesionRam($cliente)
{
    $_SESSION["sesionIniciada"] = "";
    $_SESSION["id"] = $cliente->getId();
    $_SESSION["email"] = $cliente->getEmail();
    $_SESSION["nombre"] = $cliente->getNombre();
}

    function generarCookieRecuerdame($email)
{
    
    // Creamos un código cookie muy complejo (pero no necesariamente único).
    $codigoCookie = generarCadenaAleatoria(); // Random...

    // TODO convertir a nuevo modelo DAO...

    // Anotamos el código cookie en nuestra BD.
    $claseDao = new DAO();
    $pdo = $claseDao::$pdo = $claseDao::obtenerPdoConexionBd();
    $sql = "UPDATE cliente SET codigoCookie=? WHERE id=?";
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([$codigoCookie, $email]);

    // Para una seguridad óptima conveno = self::$pdo = self::obtenerPdoConexidriá anotar en la BD la fecha
    // de caducidad de la cookie y no aceptar ninguna cookie pasada dicha fecha.

    establecerCookieRecuerdame($email, $codigoCookie);
}
function generarCadenaAleatoria()
{
    for ($s = '', $i = 0, $z = strlen($a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')-1; $i != 32; $x = rand(0,$z), $s .= $a{$x}, $i++);
    return $s;
}

    
    function establecerCookieRecuerdame($identificador, $codigoCookie)
{
    // Enviamos el código cookie al cliente, junto con su identificador.
    setcookie("email", $identificador, time() + 24*60*60); // Un mes sería: +30*24*60*60
    setcookie("codigoCookie", $codigoCookie, time() + 24*60*60); // Un mes sería: +30*24*60*60
}
