<?php
session_start();
require '../config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['username'] ?? '');
    $senha = $_POST['password'] ?? '';

    if ($nome === '' || $senha === '') {
        $error = 'Preencha usuário e senha.';
    } else {
        $sql = "SELECT * FROM usuarios WHERE nome = :nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // grava id na sessão (usuário logado)
            $_SESSION['id'] = $usuario['id'];
            header('Location: ../Home/index.php');
            exit();
        } else {
            $error = 'Nome ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — DrawQuest</title>
    <link rel="stylesheet" href="styleLogin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">
            <h1>DrawQuest</h1>
        </div>
        <nav>
            <a href="../Home/index.php">Home</a>
            <a href="../SobreNos/paginaSN.php">Sobre Nós</a>
            <a href="../Personagens/paginaPerso.php">Personagens</a>
            <a href="../SobreNos/Contato/contato.php">Contato</a>
        </nav>
    </header>

    <main class="login">
        <h2>Login</h2>

        <?php if ($error): ?>
            <div style="color:#ffb3b3; margin-bottom:16px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <label for="username">Nome de Usuário</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Entrar</button>
        </form>

        <div class="cadastro">
            <p>Ainda não tem conta? <a href="cadastrar.php">Cadastre-se aqui</a></p>
        </div>
    </main>

    <footer>
      © 2025 DrawQuest — Crie, jogue e viva suas histórias!
    </footer>
</body>
</html>