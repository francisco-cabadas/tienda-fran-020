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
    Nombre:<input type="text" name="nombre" required>
    Descripion:<input type="text" name="descripcion" required>
    Precio:<input type="number" min="0" step="any" name="precio" required>
    <input type="submit">
    </form>
<a href="../cli/productos-listado.php">Volver a listado</a>


</body>
</html>