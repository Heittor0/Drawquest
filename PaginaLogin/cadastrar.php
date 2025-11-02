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
              <?php if (empty($_SESSION['id'])): ?>
       <a class="aba-link" href="../PaginaLogin/login.php">Login</a>
      <?php else: ?>
        <a href="../config/sair.php">Deslogar</a>
      <?php endif; ?>
                  <a href="../Home/index.php">Home</a>
            <a href="../Sobre Nós/paginaSN.php">Sobre Nós</a>
      <a href="../Personagens/paginaPerso.php">Personagens</a>
      <a href="../Sobre Nós/Contato/contato.php">Contato</a>
        </nav>
    </header>
    <main class="login">
        <h2>Cadastrar</h2>
        <form action="" method="post">
            <label for="username">Nome de Usuário</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>

            <label for="password">email</label>
            <input type="email" id="email" name="email" required>

            <button type="submit" class="btn">Cadastrar</button>
        </form>
        <main>
            <p class="cadastro">já tem uma conta? <a href="login.php">logue aqui!</a></p>
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
            $email = $_POST['email'];
            $senha = password_hash($_POST['password'], PASSWORD_DEFAULT);
            //Qual codigo faz oq
            // Esse daqui Cria uma string para contar quantos registros tem na tabela
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
            //Prepara uma consulta no SQL(neon) usando o pdo
            $stmt = $pdo -> prepare($sql);
            //Prepara a consulta SQL para ser executada no banco de dados
            $stmt->bindParam(':email', $email);
            //Associa a variavel email ao parametro no SQL
            $stmt->execute();
            //Executa a consulta preparada no Banco de dados
            $existe = $stmt->fetchColumn();
            if($existe > 0 ){
                echo "<div style='color:#776472;'>Este email já existe.</div>";
            } else {
                $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', $senha);
                if ($stmt->execute()) {
                    echo "<div style='color:#558B6E;'>Cadastro realizado com sucesso!</div>";
                } else {
                    echo "<div style='color:#776472;'>Erro ao cadastrar.</div>";
                }
            }
            
        }

?>