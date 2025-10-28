<?php
session_start();
require "../config/config.php";





?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DrawQuest — Personagens Prontos para RPG</title>
  <link rel="stylesheet" href="styleHome.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <div class="logo">
      <h1>DrawQuest</h1>
    </div>
    <nav>
        <?php if (empty($_SESSION['id'])): ?>
-        <a class="aba-link" href="../PaginaLogin/login.php">Login</a>
+        
      <?php else: ?>
        <a href="../config/sair.php">Deslogar</a>
      <?php endif; ?>
      <a href="../sobreNos/paginaSN.php">Sobre Nós</a>
      <a href="../Personagens/paginaPerso.php">Personagens</a>
      <a href="../sobrenos/Contato/contato.php">Contato</a>
    </nav>
  </header>

  <section class="banner">
    <img src="https://wallpaperflare.com/static/1016/101/1016/fantasy-rpg-banner-artwork-wallpaper.jpg" alt="Banner DrawQuest">
  </section>

  <section class="herois">
    <h2>Dê vida à sua aventura com personagens únicos e prontos para jogar!</h2>
    <p>Arte original, estilos variados e fichas completas para seus mundos de RPG.</p>
    <a href="#galeria" class="btn">Ver personagens</a>
  </section>

  <section id="galeria" class="galeriaHerois">
    <h3>Escolha seu herói!</h3>
    <div class="galeria-navegacao">
      <button class="seta esquerda">◀</button>
      <div class="cards">
        <div class="card"><img src="https://i.imgur.com/DMXG4nK.png" alt="Personagem"><h4>Personagem 1</h4><p>R$??</p><a href="Personagens/ver_produtos.php?id=1" class="btn">Ver detalhes</a></div>
        <div class="card"><img src="https://i.imgur.com/8Kf3CVm.png" alt="Personagem"><h4>Personagem 2</h4><p>R$??</p><a href="Personagens/ver_produtos.php?id=2" class="btn">Ver detalhes</a></div>
        <div class="card"><img src="https://i.imgur.com/DMXG4nK.png" alt="Personagem"><h4>Personagem 3</h4><p>R$??</p><a href="Personagens/ver_produtos.php?id=3" class="btn">Ver detalhes</a></div>
        <div class="card"><img src="https://i.imgur.com/8Kf3CVm.png" alt="Personagem"><h4>Personagem 4</h4><p>R$??</p><a href="Personagens/ver_produtos.php?id=4" class="btn">Ver detalhes</a></div>
        <div class="card"><img src="https://i.imgur.com/DMXG4nK.png" alt="Personagem"><h4>Personagem 5</h4><p>R$??</p><a href="Personagens/ver_produtos.php?id=5" class="btn">Ver detalhes</a></div>
        <div class="card"><img src="https://i.imgur.com/8Kf3CVm.png" alt="Personagem"><h4>Personagem 6</h4><p>R$??</p><a href="Personagens/ver_produtos.php?id=6" class="btn">Ver detalhes</a></div>
      </div>
      <button class="seta direita">▶</button>
    </div>
  </section>

  <footer>
    © 2025 DrawQuest — Crie, jogue e viva suas histórias!
  </footer>

  <script>
    const esquerda = document.querySelector('.seta.esquerda');
    const direita = document.querySelector('.seta.direita');
    const cards = document.querySelector('.cards');

    esquerda.addEventListener('click', () => {
      cards.scrollBy({ left: -300, behavior: 'smooth' });
    });

    direita.addEventListener('click', () => {
      cards.scrollBy({ left: 300, behavior: 'smooth' });
    });
  </script>
</body>
</html>




