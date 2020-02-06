<?php

// Esta función redirige a otra página y deja de ejecutar el PHP que la llamó:
function redireccionar($url) {
    header("Location: $url");
    exit();
}