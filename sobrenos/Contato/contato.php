<?php
session_start();
require "../../config/config.php";





?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato — DrawQuest</title>
    <link rel="stylesheet" href="Stylecontato.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
    <nav>
          <?php if (empty($_SESSION['id'])): ?>
       <a class="aba-link" href="../../paginalogin/login.php">Login</a>
       
      <?php else: ?>
        <a href="../config/sair.php">Deslogar</a>
      <?php endif; ?>
              <a href="../../index.php">Home</a>
        <a href="../paginaSN.php">Sobre Nós</a>
      <a href="../../Personagens/paginaPerso.php">Personagens</a>
      

    </nav>
</header>
    <main class="contato">
        <h2>Fale com a gente</h2>
        <p>Envie sua mensagem e responderemos o mais rápido possível.</p>

        <form action="mailto:algumemail@email.com" method="post" enctype="text/plain">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" required rows="6"></textarea>

            <button type="submit" class="btn">Enviar</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 DrawQuest. Todos os direitos reservados.</p>
    </footer>
</body>
</html>