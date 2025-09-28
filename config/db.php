<?php
$conex = mysqli_connect("localhost", "root", "", "fime inclusivo");

if (!$conex) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
