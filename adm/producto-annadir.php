<?php

require_once "../_com/comunes-app.php";

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
    <form action="producto-annadir-ejecutar.php">
    Añadir un nuevo producto:
    Nombre:<input type="text" name="nombre">
    Descripion:<input type="text" name="descripcion">
    Precio:<input type="number" step="any" name="precio">
    <input type="submit">
    </form>
<a href="../cli/productos-listado.php">Volver a listado</a>


</body>
</html>