<?php
session_start();
require "../config/config.php";

$sql = "SELECT id, nome, pdf, texto, imagem, preco, classe, tempodepostagem
        FROM produtos
        ORDER BY tempodepostagem DESC";

$produtos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);



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
      <a class="aba-link" href="../PaginaLogin/login.php">Login</a>
   
      <?php else: ?>
        <a href="../config/sair.php">Deslogar</a>
      <?php endif; ?>
      <a href="../sobrenos/paginaSN.php">Sobre Nós</a>
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
        <?php foreach ($produtos as $pro): ?>
          <?php $id = (int)($pro['id'] ?? 0); ?>
          <div class="card" role="link" tabindex="0" data-id="<?= $id ?>" aria-label="Ver produto <?= htmlspecialchars($pro['nome'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8') ?>">
            <?php
              // Se imagem for um caminho string use-o; caso contrário exiba placeholder.
              if (!empty($pro['imagem']) && is_string($pro['imagem'])) {
                  $img = strpos($pro['imagem'], 'http') === 0 ? $pro['imagem'] : ('../' . ltrim($pro['imagem'], '/\\'));
              } else {
                  $img = 'https://i.imgur.com/DMXG4nK.png';
              }
            ?>
            <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="Personagem">
            <h4><?= htmlspecialchars($pro['nome'] ?? $pro['titulo'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8') ?></h4>
            <p><strong>Classe:</strong> <?= htmlspecialchars($pro['classe'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Estilo:</strong> <?= htmlspecialchars($pro['estilo'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
            <p><?= nl2br(htmlspecialchars($pro['texto'] ?? $pro['assunto'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
            <a class="btn" href="index.php?id=<?= $id ?>" onclick="event.stopPropagation();">Comprar / Ver</a>
        </div>
          </div>
        <?php endforeach; ?>
  
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

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.card').forEach(function(card){
        card.style.cursor = 'pointer';

        card.addEventListener('click', function (e) {
          // se o clique for em um link interno (ex: Comprar) não interferir
          if (e.target.closest('a')) return;
          var id = card.dataset.id;
          if (id) window.location.href = '../Personagens/ver_produtos.php?id=' + encodeURIComponent(id);
        });

        card.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            var id = card.dataset.id;
            if (id) window.location.href = '../Personagens/ver_produtos.php?id=' + encodeURIComponent(id);
          }
        });
      });
    });
  </script>
</body>
</html>




