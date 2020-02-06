<?php


session_start();


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>inicio de sesion</title>
</head>
<style>
    body {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        justify-content: center;
        margin: 0;
    }

    h1 {
        margin: 0;
        padding: 20px;
        width: 100%;
        text-align: center;
        height: 50px;
        background-color: cadetblue;
        color: white;
    }

    .formulario {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    form {
        width: 30%;
        display: flex;
        flex-wrap: wrap;
        background-color: white;
        padding: 15px;
    }

    form p {
        width: 100%;

    }

    form input[type="text"] {
        width: 100%;
        padding: 10px;
    }

    form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
    }

    form input[type="submit"] {
        width: 100%;
        padding: 5px;
    }

</style>
<body>
<?php
if (isset($_REQUEST["incorrecto"])) {
    echo "<p>Usuario o contraseña incorrectos.</p>";
}
if (isset($_REQUEST["sesionCerrada"])) {
    echo "<p>Ha salido correctamente. Su sesión está ahora cerrada.</p>";
}
?>
<h1> Inicio de sesion</h1>
<div class="formulario">
    <form action="productos-listado.php" method="post">
        <p>usuario:</p>
        <input name="email" type="email" placeholder="Escribe tu correo electronio">
        <br><br>
        <p>contraseña:</p>
        <input name="contrasenna" type="password" placeholder="Contraseña">
        <br><br>
        <input type="checkbox" name="guardar_clave" value="1"> recordar el usuario en este ordenador
        <br><br>

        <input type="submit" value="Iniciar sesión" style="border-radius: 10px;background-color: cadetblue">
        <br><br>
    </form>
</div>


</body>
</html>