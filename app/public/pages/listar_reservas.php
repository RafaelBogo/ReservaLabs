<?php
$protegido = false;
require_once('../../config/database.php');
require_once('../../includes/session.inc.php');

$laboratorios = array();
$query = $bancoDados->prepare("SELECT id, nome FROM Laboratorio WHERE liberado = 1 ORDER BY nome");
if ($query->execute()) {
    if ($query->rowCount() > 0) {
        $laboratorios = $query->fetchAll(PDO::FETCH_OBJ);
    }
}
if (isset($_SESSION['usuario'])) {
    $usuario = unserialize(base64_decode($_SESSION['usuario']));
    $userId = $usuario['id'];
} else {
    header("Location: login.php");
    exit;
}

$isAdmin = false;
$sql = "SELECT tipo FROM Pessoa WHERE id = :id";
$stmt = $bancoDados->prepare($sql);
$stmt->bindParam(':id', $userId);
$stmt->execute();
if ($stmt->rowCount() == 1) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($user['tipo'] === 'A');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserva_id']) && $isAdmin) {
    $reserva_id = $_POST['reserva_id'];
    $query = $bancoDados->prepare("DELETE FROM Reserva WHERE id = :id");
    $query->bindParam(':id', $reserva_id);
    if ($query->execute()) {
        header("Refresh:0");// Atualiza a lista de reservas depois de excluir
    } else {
        $erro = "Erro ao excluir a reserva. Tente novamente.";
    }
}
$selectedLab = null;
$reservas = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['laboratorio_id'])) {
    $selectedLab = $_POST['laboratorio_id'];
    $query = $bancoDados->prepare("SELECT r.id, r.data, r.hora_inicio, r.hora_fim, r.descricao, p.nome AS pessoa
                                   FROM Reserva r 
                                   JOIN Pessoa p ON r.pessoa_id = p.id
                                   WHERE r.laboratorio_id = :laboratorio_id 
                                   ORDER BY r.data DESC, r.hora_inicio DESC");
    $query->bindParam(':laboratorio_id', $selectedLab);
    if ($query->execute()) {
        if ($query->rowCount() > 0) {
            $reservas = $query->fetchAll(PDO::FETCH_OBJ);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Laboratório</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </nav>
        <h1 class="my-4">Ver Reservas de Laboratório</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="laboratorio_id">Selecionar Laboratório</label>
                <select class="form-control" id="laboratorio_id" name="laboratorio_id" required>
                    <option value="">Selecione o laboratório</option>
                    <?php foreach ($laboratorios as $laboratorio): ?>
                        <option value="<?= $laboratorio->id ?>" <?= ($selectedLab == $laboratorio->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($laboratorio->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ver Reservas</button>
        </form>
        <?php if ($selectedLab): ?>
            <div class="mt-4">
                <h2>Reservas para o Laboratório: <?= htmlspecialchars($laboratorios[array_search($selectedLab, array_column($laboratorios, 'id'))]->nome) ?></h2>
                <?php if (!empty($reservas)): ?>
                    <ul class="list-group">
                        <?php foreach ($reservas as $reserva): ?>
                            <li class="list-group-item">
                                <strong>Pessoa:</strong> <?= htmlspecialchars($reserva->pessoa) ?><br>
                                <strong>Data:</strong> <?= htmlspecialchars($reserva->data) ?><br>
                                <strong>Hora de Início:</strong> <?= htmlspecialchars($reserva->hora_inicio) ?><br>
                                <strong>Hora de Fim:</strong> <?= htmlspecialchars($reserva->hora_fim) ?><br>
                                <strong>Descrição:</strong> <?= htmlspecialchars($reserva->descricao) ?><br>
                                <?php if ($isAdmin): ?>
                                    <form method="post" action="" class="mt-2">
                                        <input type="hidden" name="reserva_id" value="<?= $reserva->id ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Não há reservas para este laboratório.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
