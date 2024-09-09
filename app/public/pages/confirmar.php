<?php
session_start();

require_once('../../config/database.php');

$erro = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_verificacao = $_POST['codigo_verificacao'];

    if (isset($_SESSION['codigo_verificacao']) && $_SESSION['codigo_verificacao'] == $codigo_verificacao) {
        $nome = $_SESSION['nome'];
        $email = $_SESSION['email'];
        $senha = password_hash($_SESSION['senha'], PASSWORD_DEFAULT);
        $tipo = $_SESSION['tipo'];

        $query = $bancoDados->prepare("INSERT INTO Pessoa (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
        $query->bindParam(':nome', $nome);
        $query->bindParam(':email', $email);
        $query->bindParam(':senha', $senha);
        $query->bindParam(':tipo', $tipo);

        if ($query->execute()) {
            session_unset();
            session_destroy();

            header('Location: login.php');
            exit;
        } else {
            $erro = 'Erro ao salvar os dados. Tente novamente.';
        }
    } else {
        $erro = 'Código de verificação inválido.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Confirmar Cadastro</title>
    <style>
        .card {
            margin-top: 50px;
            padding: 20px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="card">
            <form action="<?= $_SERVER['PHP_SELF']?>" method="post">
                <h2 class="text-center">Confirmar Cadastro</h2>
                <div class="form-group">
                    <label for="codigo_verificacao">Código de Verificação</label>
                    <input type="text" class="form-control" name="codigo_verificacao" id="codigo_verificacao" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Confirmar</button>
                <?php if ($erro): ?>
                    <div class="alert alert-danger mt-3">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>
            </form>
            <a class="d-flex justify-content-center mt-3" href="cadastro.php">Voltar ao Cadastro</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
