<?php
require_once('../../config/database.php');
require_once('../../includes/session.inc.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = unserialize(base64_decode($_SESSION['usuario']));
$userId = $usuario['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserva_id'])) {
    $reserva_id = $_POST['reserva_id'];

    $query = $bancoDados->prepare("DELETE FROM Reserva WHERE id = :id AND pessoa_id = :pessoa_id");
    $query->bindParam(':id', $reserva_id);
    $query->bindParam(':pessoa_id', $userId);

    if ($query->execute()) {
        header("Location: home.php");
        exit;
    } else {
        echo "Erro ao excluir a reserva. Tente novamente.";
    }
} else {
    header("Location: home.php");
    exit;
}
?>
