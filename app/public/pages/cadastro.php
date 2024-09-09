<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

require_once('../../config/database.php');
require_once('../../../vendor/autoload.php');

$id = null;
$nome = null;
$email = null;
$senha = null;
$tipo = 'U';
$erro = false;
$erroNome = null;
$erroSenha = null;
$erroEmail = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    if (empty(trim($nome))) {
        $erroNome = 'Nome não pode estar em branco';
        $erro = true;
    }
    if (empty(trim($email))) {
        $erroEmail = 'Email não pode estar em branco!';
        $erro = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erroEmail = 'Email inválido!';
        $erro = true;
    }
    if (empty(trim($senha))) {
        $erroSenha = 'Senha não pode estar em branco!';
        $erro = true;
    }
    if (!$erro) {
        $query = $bancoDados->prepare('SELECT id FROM Pessoa WHERE email = :email');
        $query->bindParam(':email', $email);
        $query->execute();
        if ($query->rowCount() == 0) {
            $codigo_verificacao = rand(100000, 999999);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'rafaelbogo52@gmail.com';
                $mail->Password = 'cyyfkwjqjntthlis'; // Senha para o SMTP GMAIL
                $mail->Port = 587;
                $mail->CharSet = "UTF-8";
                $mail->setFrom('rafaelbogo52@gmail.com', 'Reserva de Laboratórios');
                $mail->addAddress($email, $nome);
                $mail->isHTML(true);
                $mail->Subject = 'Confirmação de Cadastro';
                $mail->Body = "<h1>Confirmação de Cadastro</h1>
                               <p>Olá, {$nome}. Seu cadastro foi realizado com sucesso.</p>
                               <p>Use o código abaixo para confirmar seu cadastro:</p>
                               <h2>{$codigo_verificacao}</h2>";

                $mail->send();

                $_SESSION['nome'] = $nome;
                $_SESSION['email'] = $email;
                $_SESSION['senha'] = $senha;
                $_SESSION['tipo'] = $tipo;
                $_SESSION['codigo_verificacao'] = $codigo_verificacao;

                header('Location: confirmar.php');
                exit;
            } catch (Exception $e) {
                $erroEmail = "Erro ao enviar e-mail: {$mail->ErrorInfo}";
            }
        } else {
            $erroEmail = 'Email já está em uso';
        }
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
    <title>Cadastro de Usuário</title>
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
                <h2 class="text-center">Cadastro de usuário</h2>
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" class="form-control" name="nome" id="nome" value="<?= htmlspecialchars($nome) ?>">
                    <span class="text-danger"><?= htmlspecialchars($erroNome) ?></span>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($email) ?>">
                    <span class="text-danger"><?= htmlspecialchars($erroEmail) ?></span>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" class="form-control" name="senha" id="senha">
                    <span class="text-danger"><?= htmlspecialchars($erroSenha) ?></span>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo de Usuário</label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="U" <?= ($tipo == 'U') ? 'selected' : '' ?>>Usuário Comum</option>
                        <option value="A" <?= ($tipo == 'A') ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Salvar</button>
            </form>
            <a class="d-flex justify-content-center mt-3" href="login.php">Entrar</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
