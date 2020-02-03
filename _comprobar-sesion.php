<?php
require_once "_utilidades.php";

session_start(); // Esto crea una nueva sesión-RAM o recupera la sesión RAM existente.

if (isset($_SESSION["sesionIniciada"])) { // SÍ hay sesión-usuario iniciada en la sesión-RAM.

    // >>> NO HACEMOS NADA. DEJAMOS QUE SE CONTINÚE EJECUTANDO EL PHP QUE NOS LLAMÓ... >>>

} else { // NO hay sesión-usuario iniciada en la sesión-RAM.

    if (isset($_COOKIE["identificador"])) {

        // Comprobar con BD...
        $cliente = obtenerUsuario(null, null, $_COOKIE["identificador"]);
        /* si existe un cliente con ese identificador nos devolvera la instancia de tipo cliente
         * en caso contrario nos devolvera un null
        */
        if ($cliente) {
            // dar por iniciada la sesión
            $_SESSION["sesionIniciada"] = true;
            $_SESSION["id"] = $cliente['id'];
            $_SESSION["identificador"] = $cliente["email"];
            $_SESSION["nombre"] = $cliente["nombre"];
            // renovar la cookie (su caducidad)
            generarCookieRecuerdame($cliente["email"]);
        } else {
            // borrar la cookie mala que nos están enviando
            // (si no, la enviarán otra vez, y otra, y otra...)
            borrarCookieRecuerdame($_COOKIE["identificador"]);
        }
    }

    if (isset($_REQUEST["identificador"])) { // SÍ hay formulario enviado. Lo comprobaremos contra la BD.

        $cliente = obtenerUsuario($_REQUEST["identificador"], $_REQUEST["contrasenna"], null);


        /* si existe un cliente con ese correo electronico nos devolvera la instancia de tipo cliente
         * en caso contrario nos devolvera un null
        */
        if ($cliente){
            if ($_REQUEST["contrasenna"] == $cliente["contrasenna"]) {
                // Recuperar los datos adicionales del usuario que acaba de iniciar sesión.
                // Marcar la sesión-usuario como iniciada:

                $_SESSION["sesionIniciada"] = true;
                $_SESSION["id"] = $cliente["id"];
                $_SESSION["identificador"] = $_REQUEST["identificador"];
                $_SESSION["nombre"] = $cliente["nombre"];

                if (isset($_REQUEST["recuerdame"])){
                    generarCookieRecuerdame($_REQUEST["identificador"]);
                }

            } else { // en caso de que no coincida la contrasenna con la de la BDD
                redireccionar("iniciar-sesion.php?incorrecto=true");
            }
        }
    }
}



