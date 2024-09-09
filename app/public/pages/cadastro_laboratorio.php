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

$nome = $numero_computadores = $bloco = $sala = $erro = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $numero_computadores = trim($_POST['numero_computadores']);
    $bloco = trim($_POST['bloco']);
    $sala = trim($_POST['sala']);
    if (empty($nome) || empty($numero_computadores) || empty($bloco) || empty($sala)) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $query = $bancoDados->prepare("INSERT INTO Laboratorio (nome, numero_computadores, bloco, sala, liberado) VALUES (:nome, :numero_computadores, :bloco, :sala, 1)");
        $query->bindParam(':nome', $nome);
        $query->bindParam(':numero_computadores', $numero_computadores);
        $query->bindParam(':bloco', $bloco);
        $query->bindParam(':sala', $sala);
        if ($query->execute()) {
            header("Location: home.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar laboratório.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Laboratório</title>
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
        <h1 class="my-4">Cadastro de Laboratório</h1>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="post" action="cadastro_laboratorio.php">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
            </div>
            <div class="form-group">
                <label for="numero_computadores">Número de Computadores</label>
                <input type="number" class="form-control" id="numero_computadores" name="numero_computadores" value="<?= htmlspecialchars($numero_computadores) ?>" required>
            </div>
            <div class="form-group">
                <label for="bloco">Bloco</label>
                <input type="text" class="form-control" id="bloco" name="bloco" value="<?= htmlspecialchars($bloco) ?>" required>
            </div>
            <div class="form-group">
                <label for="sala">Sala</label>
                <input type="text" class="form-control" id="sala" name="sala" value="<?= htmlspecialchars($sala) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
