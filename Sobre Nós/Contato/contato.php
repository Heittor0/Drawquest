<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Stylecontato.css">
    <link hrfef="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
    <nav>
              <a href="../../Home/index.php">Home</a>
        <a href="../paginaSN.php">Sobre Nós</a>
      <a href="../../Personagens/paginaPerso.php">Personagens</a>
      

    </nav>
</header>
    <main class="contato">
        <h2>Fale com a gente</h2>
        <p>Envie sua mensagem e responderemos o mais rápido possível.</p>

        <form action="mailto:algumemail@email.com" method="post" enctype="text/plain"></form>
            <label for="nome">Nome</label>
            <input type="email" id="nome" name="nome" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="mensagem">Mensagem</label>
            <input type="mensagem" id="mensagem" name="mensagem" required>

            <button type="submit" class="btn">Enviar</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 DrawQuest. Todos os direitos reservados.</p>
    </footer>
</body>
</html>