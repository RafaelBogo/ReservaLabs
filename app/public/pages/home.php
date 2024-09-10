<?php
$protegido = false;
require_once('../../config/database.php');
require_once('../../includes/session.inc.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = unserialize(base64_decode($_SESSION['usuario']));
$username = $usuario['nome'] ?? '';
$userId = $usuario['id'] ?? '';
$userEmail = $usuario['email'] ?? '';

// Verifica se o usuário é administrador
$isAdmin = false;
$sql = "SELECT tipo FROM Pessoa WHERE id = :id";
$stmt = $bancoDados->prepare($sql);
$stmt->bindParam(':id', $userId);
$stmt->execute();
if ($stmt->rowCount() == 1) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($user['tipo'] === 'A');
}

// Consulta para recuperar laboratórios
$laboratorios = array();
$query = $bancoDados->prepare("SELECT id, nome, numero_computadores FROM Laboratorio WHERE liberado = 1 ORDER BY nome");
$query->execute();
if ($query->rowCount() > 0) {
    $laboratorios = $query->fetchAll(PDO::FETCH_OBJ);
}

// Consulta para recuperar usuários
$usuarios = array();
if ($isAdmin) {
    $query = $bancoDados->prepare("SELECT id, nome FROM Pessoa ORDER BY nome");
    $query->execute();
    if ($query->rowCount() > 0) {
        $usuarios = $query->fetchAll(PDO::FETCH_OBJ);
    }
}

$reservaErro = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservar'])) {
    $laboratorio_id = $_POST['laboratorio_id'];
    $data = $_POST['data'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $descricao = $_POST['descricao'];
    $pessoa_id = $isAdmin && isset($_POST['pessoa_id']) ? $_POST['pessoa_id'] : $userId;

    $query = $bancoDados->prepare("SELECT id FROM Reserva WHERE laboratorio_id = :laboratorio_id AND data = :data AND hora_inicio = :hora_inicio AND hora_fim = :hora_fim");
    $query->bindParam(':laboratorio_id', $laboratorio_id);
    $query->bindParam(':data', $data);
    $query->bindParam(':hora_inicio', $hora_inicio);
    $query->bindParam(':hora_fim', $hora_fim);
    $query->execute();
    if ($query->rowCount() > 0) {
        $reservaErro = "Já existe uma reserva para esse laboratório, data e horário.";
    } else {
        $query = $bancoDados->prepare("INSERT INTO Reserva (pessoa_id, laboratorio_id, data, hora_inicio, hora_fim, descricao) VALUES (:pessoa_id, :laboratorio_id, :data, :hora_inicio, :hora_fim, :descricao)");
        $query->bindParam(':pessoa_id', $pessoa_id);
        $query->bindParam(':laboratorio_id', $laboratorio_id);
        $query->bindParam(':data', $data);
        $query->bindParam(':hora_inicio', $hora_inicio);
        $query->bindParam(':hora_fim', $hora_fim);
        $query->bindParam(':descricao', $descricao);

        if ($query->execute()) {
            header("Location: home.php");
            exit;
        } else {
            $reservaErro = "Erro ao realizar a reserva. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Bem-vindo, <?php echo htmlspecialchars($username); ?>!</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="listar_reservas.php">Todas as Reservas</a>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cadastro_laboratorio.php">Cadastrar Laboratório</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="excluir_laboratorios_usuarios.php">Excluir</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="mt-4">
            <h2>Fazer uma Nova Reserva</h2>
            <form method="post" action="home.php">
                <?php if ($isAdmin): ?>
                    <div class="form-group">
                        <label for="pessoa_id">Selecionar Usuário</label>
                        <select class="form-control" id="pessoa_id" name="pessoa_id" required>
                            <option value="">Selecione o usuário</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario->id ?>"><?= htmlspecialchars($usuario->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="laboratorio_id">Laboratório</label>
                    <select class="form-control" id="laboratorio_id" name="laboratorio_id" required onchange="updateComputadores()">
                        <option value="">Selecione o laboratório</option>
                        <?php foreach ($laboratorios as $laboratorio): ?>
                            <option value="<?= $laboratorio->id ?>" data-computadores="<?= $laboratorio->numero_computadores ?>"><?= htmlspecialchars($laboratorio->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="numero_computadores">Número de Computadores</label>
                    <input type="number" class="form-control" id="numero_computadores" disabled>
                </div>
                <div class="form-group">
                    <label for="data">Data</label>
                    <input type="date" class="form-control" id="data" name="data" required>
                </div>
                <div class="form-group">
                    <label for="hora_inicio">Hora de Início</label>
                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                </div>
                <div class="form-group">
                    <label for="hora_fim">Hora de Fim</label>
                    <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="reservar">Reservar</button>
                <?php if ($reservaErro): ?>
                    <div class="alert alert-danger mt-3">
                        <?= htmlspecialchars($reservaErro) ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="mt-4">
            <h2>Reservas Recentes</h2>
            <?php
            $sql = "SELECT r.id, r.data, r.hora_inicio, r.hora_fim, r.descricao, l.nome AS laboratorio 
                    FROM Reserva r 
                    JOIN Laboratorio l ON r.laboratorio_id = l.id 
                    WHERE r.pessoa_id = :pessoa_id 
                    ORDER BY r.data DESC, r.hora_inicio DESC 
                    LIMIT 5";
            $stmt = $bancoDados->prepare($sql);
            $stmt->bindParam(':pessoa_id', $userId);
            $stmt->execute();
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php if (!empty($reservas)): ?>
                <ul class="list-group">
                    <?php foreach ($reservas as $reserva): ?>
                        <li class="list-group-item">
                            <strong>Laboratório:</strong> <?php echo htmlspecialchars($reserva['laboratorio']); ?><br>
                            <strong>Data:</strong> <?php echo htmlspecialchars($reserva['data']); ?><br>
                            <strong>Hora de Início:</strong> <?php echo htmlspecialchars($reserva['hora_inicio']); ?><br>
                            <strong>Hora de Fim:</strong> <?php echo htmlspecialchars($reserva['hora_fim']); ?><br>
                            <strong>Descrição:</strong> <?php echo htmlspecialchars($reserva['descricao']); ?><br>
                            <form method="post" action="excluir_reserva.php" class="mt-2">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Você não tem reservas recentes.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function updateComputadores() {
            var laboratorioSelect = document.getElementById('laboratorio_id');
            var selectedOption = laboratorioSelect.options[laboratorioSelect.selectedIndex];
            var numComputadores = selectedOption.getAttribute('data-computadores');
            document.getElementById('numero_computadores').value = numComputadores;
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
