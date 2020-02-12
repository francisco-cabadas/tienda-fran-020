<?php

require_once "dao.php";
require_once "clases.php";
require_once "utilidades.php";

function sessionStartSiNoLoEsta()
{
    if(!isset($_SESSION)) {
        session_start();
    }
}

// Comprueba si hay sesión-usuario iniciada en la sesión-RAM.
function haySesionIniciada()
{
    sessionStartSiNoLoEsta();
    return isset($_SESSION['sesionIniciada']);
}

function vieneFormularioDeInicioDeSesion()
{
    return isset($_REQUEST['email']);
}

function vieneCookieRecuerdame()
{
    return isset($_COOKIE["identificador"]);
}

function garantizarSesion()
{
    sessionStartSiNoLoEsta();

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
                redireccionar("../cli/sesion-inicio.php");
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
                redireccionar("../cli/sesion-inicio.php?incorrecto=true");
            }
        } else { // NO hay ni sesión, ni cookie, ni formulario enviado.
            // REDIRIGIMOS PARA QUE NO SE VISUALICE CONTENIDO PRIVADO:
            redireccionar("../cli/sesion-inicio.php");
        }
    }
}

function establecerCookieRecuerdame($identificador, $codigoCookie)
{
    // Enviamos el código cookie al cliente, junto con su identificador.
    setcookie("email", $identificador, time() + 24*60*60); // Un mes sería: +30*24*60*60
    setcookie("codigoCookie", $codigoCookie, time() + 24*60*60); // Un mes sería: +30*24*60*60
}

function generarCadenaAleatoria()
{
    for ($s = '', $i = 0, $z = strlen($a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')-1; $i != 32; $x = rand(0,$z), $s .= $a[$x], $i++);
    return $s;
}

function generarCookieRecuerdame($email)
{
    // Creamos un código cookie muy complejo (pero no necesariamente único).
    $codigoCookie = generarCadenaAleatoria(); // Random...

    // TODO convertir a nuevo modelo DAO...

    // Anotamos el código cookie en nuestra BD.
    $pdo = conectarBd();
    $sql = "UPDATE usuario SET codigoCookie=? WHERE identificador=?";
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([$codigoCookie, $email]);

    // Para una seguridad óptima convendriá anotar en la BD la fecha
    // de caducidad de la cookie y no aceptar ninguna cookie pasada dicha fecha.

    establecerCookieRecuerdame($email, $codigoCookie);
}

function borrarCookieRecuerdame($identificador)
{
    // TODO convertir a nuevo modelo DAO...

    // Eliminamos el código cookie de nuestra BD.
    $pdo = conectarBd();
    $sql = "UPDATE usuario SET codigoCookie=NULL WHERE identificador=?";
    $sentencia = $pdo->prepare($sql);
    $sentencia->execute([$identificador]);

    setcookie("email", "", time() - 3600); // Tiempo en el pasado, para (pedir) borrar la cookie.
    setcookie("codigoCookie", "", time() - 3600); // Tiempo en el pasado, para (pedir) borrar la cookie.
}

function anotarDatosSesionRam($cliente)
{
    $_SESSION["sesionIniciada"] = "";
    $_SESSION["id"] = $cliente->getId();
    $_SESSION["email"] = $cliente->getEmail();
    $_SESSION["nombre"] = $cliente->getNombre();
}