<?php
$protegido = false;
require_once('../../config/database.php');
require_once('../../includes/session.inc.php');

$email = null;
$senha = null;
$erro = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $query = $bancoDados->prepare("SELECT id, nome, senha FROM Pessoa WHERE email = :email");
    $query->bindParam(":email", $email);

    if ($query->execute()) {
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_OBJ);
            if (password_verify($senha, $row->senha)) {
                $usuario = array(
                    'id' => $row->id,
                    'nome' => $row->nome,
                    'email' => $email
                );

                // Atualiza o ultimo login do usario
                $updateQuery = $bancoDados->prepare("UPDATE Pessoa SET ultimo_login = NOW() WHERE id = :id");
                $updateQuery->bindParam(':id', $row->id);
                $updateQuery->execute();

                $_SESSION['usuario'] = base64_encode(serialize($usuario));
                $bancoDados = null;
                header('Location: home.php');
                exit;
            } else {
                $erro = "Login ou senha incorretos!";
                $bancoDados = null;
            }
        } else {
            $erro = "Login ou senha incorretos!";
            $bancoDados = null;
        }
    } else {
        $erro = "Erro interno";
        $bancoDados = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin-top: 100px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
    </style>
    <title>Login de Usuário</title>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="login-container">
            <h2 class="text-center my-4">Entrar</h2>
            <form action="<?= $_SERVER['PHP_SELF']?>" method="post">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" class="form-control" name="senha" id="senha" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Enviar</button>
                <?php if (!is_null($erro)): ?>
                    <div class="alert alert-danger mt-3">
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>
            </form>
            <a class="d-flex justify-content-center mt-3" href="cadastro.php">Cadastrar</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
