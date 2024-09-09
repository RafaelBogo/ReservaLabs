<?php
session_start();

if (!isset($protegido)) {
    $protegido = false;
}

if ($protegido) {
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit;
    }
}
?>
