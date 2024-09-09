<?php
$protegido = true;
require_once('../../config/database.php');
require_once('../../includes/session.inc.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = unserialize(base64_decode($_SESSION['usuario']));
$username = $usuario['nome'];
$userId = $usuario['id'];

$isAdmin = false;
$sql = "SELECT tipo FROM Pessoa WHERE id = :id";
$stmt = $bancoDados->prepare($sql);
$stmt->bindParam(':id', $userId);
$stmt->execute();
if ($stmt->rowCount() == 1) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($user['tipo'] === 'A');
}
if (!$isAdmin) {
    header("Location: home.php");
    exit;
}

$laboratorios = [];
$usuarios = [];

$query = $bancoDados->prepare("SELECT id, nome FROM Laboratorio");
$query->execute();
$laboratorios = $query->fetchAll(PDO::FETCH_OBJ);
$query = $bancoDados->prepare("SELECT id, nome FROM Pessoa");
$query->execute();
$usuarios = $query->fetchAll(PDO::FETCH_OBJ);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['excluir_laboratorio'])) {
        $laboratorio_id = $_POST['laboratorio_id'];
        $query = $bancoDados->prepare("DELETE FROM Laboratorio WHERE id = :id");
        $query->bindParam(':id', $laboratorio_id);
        if ($query->execute()) {
            $msg = "Laboratório excluído com sucesso!";
        } else {
            $msg = "Erro ao excluir o laboratório.";
        }
    } elseif (isset($_POST['excluir_usuario'])) {
        $usuario_id = $_POST['usuario_id'];
        $query = $bancoDados->prepare("DELETE FROM Pessoa WHERE id = :id");
        $query->bindParam(':id', $usuario_id);
        if ($query->execute()) {
            $msg = "Usuário excluído com sucesso!";
        } else {
            $msg = "Erro ao excluir o usuário.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Laboratórios e Usuários</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </nav>
        <?php if (isset($msg)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <h2>Excluir Laboratório</h2>
        <form method="post" action="excluir_laboratorio_usuario.php">
            <div class="form-group">
                <label for="laboratorio_id">Selecionar Laboratório</label>
                <select class="form-control" id="laboratorio_id" name="laboratorio_id" required>
                    <?php foreach ($laboratorios as $laboratorio): ?>
                        <option value="<?= $laboratorio->id ?>"><?= htmlspecialchars($laboratorio->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-danger" name="excluir_laboratorio">Excluir Laboratório</button>
        </form>

        <h2 class="mt-5">Excluir Usuário</h2>
        <form method="post" action="excluir_laboratorio_usuario.php">
            <div class="form-group">
                <label for="usuario_id">Selecionar Usuário</label>
                <select class="form-control" id="usuario_id" name="usuario_id" required>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario->id ?>"><?= htmlspecialchars($usuario->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-danger" name="excluir_usuario">Excluir Usuário</button>
        </form>
    </div>
</body>
</html>
