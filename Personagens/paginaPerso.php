<?php
session_start();
require '../config/config.php';

// Seleciona produtos — a sua tabela produtos não tem referência a usuarios nas imagens que mostrou,
// então busca apenas os campos existentes na tabela produtos.
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
  <title>Personagens — DrawQuest</title>
  <link rel="stylesheet" href="StylePerso.css">
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
      <a href="../sobrenos/paginaSN.php">Sobre Nós</a>
      <a href="../sobrenos/Contato/contato.php">Contato</a>
    </nav>
  </header>

  <main class="personagens" >
    <h2>Galeria de Personagens</h2>
    <p>Conheça nossos heróis prontos para jogar!</p>

    <div class="cards">
      <?php foreach ($produtos as $pro): ?>
        <?php $id = (int)($pro['id'] ?? 0); ?>
        <div class="card" role="link" tabindex="0" data-id="<?= $id ?>" aria-label="Ver produto <?= htmlspecialchars($pro['nome'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8') ?>">
          <?php
           if (!empty($pro['imagem']) && is_string($pro['imagem'])) {
    // Corrige caminhos locais do Windows para URL relativa
    if (preg_match('/^[A-Z]:\\\\/i', $pro['imagem'])) {
        // remove C:\xampp\htdocs\Site-RPG-xamp\
        $img = str_replace(['C:\\xampp\\htdocs\\Site-RPG-xamp\\', '\\'], ['../', '/'], $pro['imagem']);
    } elseif (strpos($pro['imagem'], 'http') === 0) {
        $img = $pro['imagem'];
    } else {
        $img = '../' . ltrim($pro['imagem'], '/');
    }
} else {
    $img = 'https://i.imgur.com/DMXG4nK.png';
}

          ?>
          <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="Personagem">
          <h4><?= htmlspecialchars($pro['nome'] ?? $pro['titulo'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8') ?></h4>
          <p><strong>Raça:</strong> <?= htmlspecialchars($pro['classe'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
          <p><strong>Estilo:</strong> <?= htmlspecialchars($pro['estilo'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
          <p><?= nl2br(htmlspecialchars($pro['texto'] ?? $pro['assunto'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
          <p><strong>Preço:</strong> <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '-' ?></p>

          <a class="btn" href="ver_produtos.php?id=<?= $id ?>" onclick="event.stopPropagation();">Comprar / Ver</a>
        </div>
      <?php endforeach; ?>
      
    </div>
  </main>

  <script>
    // torna cada .card clicável e acessível via teclado (Enter)
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.card').forEach(function(card){
        card.style.cursor = 'pointer';

        card.addEventListener('click', function (e) {
          // se o clique for em um link interno (ex: Comprar) não interferir
          if (e.target.closest('a')) return;
          var id = card.dataset.id;
          if (id) window.location.href = 'ver_produtos.php?id=' + encodeURIComponent(id);
        });

        card.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            var id = card.dataset.id;
            if (id) window.location.href = 'ver_produtos.php?id=' + encodeURIComponent(id);
          }
        });
      });
    });
  </script>

  <footer>
    © 2025 DrawQuest — Crie, jogue e viva suas histórias!
  </footer>
</body>
</html>






