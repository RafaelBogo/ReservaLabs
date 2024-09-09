<?php 

    $protegido = true;
    require_once('../../includes/session.inc.php');
    session_destroy();
    header("Location: login.php");

?>