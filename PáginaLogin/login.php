<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login-DrawQuest</title>
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
            <a href="../Sobre Nós/paginaSN.php">Sobre Nós</a>
      <a href="../Personagens/paginaPerso.php">Personagens</a>
      <a href="../Sobre Nós/Contato/contato.php">Contato</a>
        </nav>
    </header>
    <main class="login">
        <h2>Login</h2>
        <form action="" method="post">
            <label for="username">Nome de Usuário</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            

            <button type="submit" class="btn">Entrar</button>
        </form>
        <main>
            <p class="cadastro">Ainda não tem conta? <a href="cadastrar.php">Cadastre-se aqui</a></p>
        </main>

  <footer>
    © 2025 DrawQuest — Crie, jogue e viva suas histórias!
  </footer>
</body>
</html>
</body>
</html>
<?php

        require '../config/config.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['username'];
           
            $senha = $_POST['password'];
            $sql = "SELECT * FROM usuarios WHERE nome = :nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
           
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                header("Location:../Home/index.php");
            } else {
                echo "<div style='color:#776472;'>Nome, email ou senha incorretos.</div>";
            }
        }
        ?>
?