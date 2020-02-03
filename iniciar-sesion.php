<?php
require_once "_utilidades.php";
session_start();

$mensaje = "";
if (isset($_REQUEST["incorrecto"])) {
    echo "<p>Usuario o contraseña incorrectos.</p>";
}
if (isset($_REQUEST["sesionCerrada"])) {
    echo "<p>Ha salido correctamente. Su sesión está ahora cerrada.</p>";
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php include("templates/bootstrap.php") ?>
  <title>Iniciar Sesion</title>
</head>
<body>
<?php

?>

<h1>Iniciar sesión</h1>

<div class="container col-lg-3">
  <header class="main-bg">
    <div class="ml-5 d-flex align-items-center h-100">
      <div class="title main-text"><h1>MiniFB</h1></div>
    </div>
  </header>

  <div class="section main-text d-flex align-items-center">
    <span class="ml-3"><h3>Iniciar sesión</h3></span>
  </div>
  <div id="error"><?= $mensaje ?></div>
  <form action="_comprobar-sesion.php" class="mt-3" method="POST">
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="input-email">Alias</span>
      </div>
      <input type="text" class="form-control"
             name="identificador" aria-label="Email" aria-describedby="input-email" required>
    </div>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text" id="input-pass">Contraseña</span>
      </div>
      <input type="password" class="form-control"
             name="contrasenna" aria-label="Password" aria-describedby="input-pass" required>
    </div>
    <div class="form-check ml-3">
      <input type="checkbox" class="form-check-input" id="recuerdame" name="recuerdame" value="true">
      <label for="recuerdame">Recordarme</label>
    </div>
    <div class="row d-flex justify-content-around align-items-center">
      <input type="submit" value="Entrar" class="btn btn-primary w-25 mt-2 ml-5">
      <div id="registro"><a href="#">Registrarse</a></div>
    </div>
  </form>
</div>
</body>

</html>