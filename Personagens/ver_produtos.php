<?php
session_start();
require '../config/config.php';

// pega id da query string e valida
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo 'ID inválido.';
    exit;
}

// busca o produto pelo id
$sql = "SELECT id, nome, pdf, texto, imagem, preco, classe, estilo, tempodepostagem
        FROM produtos
        WHERE id = :id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$pro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pro) {
    http_response_code(404);
    echo 'Produto não encontrado.';
    exit;
}

// trata imagem (URL/caminho ou BYTEA)
$placeholder = 'https://i.imgur.com/DMXG4nK.png';
$img = $placeholder;
if (!empty($pro['imagem'])) {
    if (is_string($pro['imagem']) && (strpos($pro['imagem'], 'http') === 0 || preg_match('/^(\/|[A-Za-z]:\\\\)/', $pro['imagem']))) {
        $img = $pro['imagem'];
    } else {
        $img = 'data:image/jpeg;base64,' . base64_encode($pro['imagem']);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pro['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8') ?> — DrawQuest</title>
    <link rel="stylesheet" href="StylePerso.css">
</head>
<body>
  <header>
    <div class="logo"><h1>DrawQuest</h1></div>
    <nav>
      <a href="paginaPerso.php">Voltar</a>
      <a href="../Home/index.php">Home</a>
    </nav>
  </header>

  <main class="personagens">
    <div class="card-single">
      <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($pro['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8') ?>">
      <h2><?= htmlspecialchars($pro['nome'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8') ?></h2>
      <p><strong>Classe:</strong> <?= htmlspecialchars($pro['classe'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
      <p><strong>Estilo:</strong> <?= htmlspecialchars($pro['estilo'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
      <p><?= nl2br(htmlspecialchars($pro['texto'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
      <p><strong>Preço:</strong> <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '-' ?></p>

      <?php if (!empty($pro['pdf'])): ?>
        <p><a href="download.php?id=<?= (int)$pro['id'] ?>">Baixar PDF</a></p>
      <?php endif; ?>
    </div>
  </main>

  <footer>© 2025 DrawQuest</footer>
</body>
</html>