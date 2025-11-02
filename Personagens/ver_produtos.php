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

// obter email do usuário (sessão) — fallback: buscar na tabela users se tiver user_id na sessão
$userEmail = '';
if (!empty($_SESSION['email']) && filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)) {
    $userEmail = $_SESSION['email'];
} elseif (!empty($_SESSION['user_id'])) {
    $stmtU = $pdo->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
    $stmtU->execute([':id' => (int) $_SESSION['user_id']]);
    $u = $stmtU->fetch(PDO::FETCH_ASSOC);
    if ($u && !empty($u['email']) && filter_var($u['email'], FILTER_VALIDATE_EMAIL)) {
        $userEmail = $u['email'];
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pro['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8') ?> — DrawQuest</title>
    <link rel="stylesheet" href="StylePerso.css?v=1.0.3">
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
      <h2><?= htmlspecialchars($pro['nome'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8') ?></h2>
<p class="price-tag"><strong>Preço:</strong> <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '-' ?></p>

<p class="description">
  <strong>Classe:</strong> <?= htmlspecialchars($pro['classe'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
  <strong>Estilo:</strong> <?= htmlspecialchars($pro['estilo'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
  <?= nl2br(htmlspecialchars($pro['texto'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
</p>
      <p><strong>Preço:</strong> <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '-' ?></p>

      <?php if (!empty($pro['pdf'])): ?>
        <p><a href="download.php?id=<?= (int)$pro['id'] ?>">Baixar PDF</a></p>
      <?php endif; ?>

   <div id="payment">
  <h3>Pagar este produto</h3>
  <label>
    Seu email:
    <input type="email" id="payerEmail" value="<?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>" placeholder="seu@exemplo.com" required>
  </label>
  <br>
  <button id="payBtn" <?= empty($pro['preco']) ? 'disabled' : '' ?>>Pagar <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '' ?></button>
  <div id="paymentResult" style="margin-top:1rem;"></div>
</div>

<!-- Modal markup -->
<div id="qrModal" class="modal-overlay">
  <div class="modal-content">
    <button class="close-modal" aria-label="Fechar">&times;</button>
    <h4>QRCode PIX</h4>
    <img id="qrModalImg" src="" alt="QR Code PIX">
    <pre id="pixCodeText"></pre>
    <button class="copy-pix">Copiar código PIX</button>
  </div>
</div>
<footer>© 2025 DrawQuest</footer>
<script>
(function(){
  const price = Number(<?= json_encode($pro['preco']) ?>);
  const payBtn = document.getElementById('payBtn');
  const emailInput = document.getElementById('payerEmail');
  const result = document.getElementById('paymentResult');

  const modal = document.getElementById('qrModal');
  const modalImg = document.getElementById('qrModalImg');
  const pixCodeText = document.getElementById('pixCodeText');
  const copyPixBtn = modal.querySelector('button.copy-pix');
  const closeModalBtn = modal.querySelector('button.close-modal');

  function showError(msg){
    result.innerHTML = '<div style="color:#b00;">'+msg+'</div>';
  }

  function openModal(imgSrc, code) {
    modalImg.src = imgSrc;
    pixCodeText.textContent = code;
    modal.style.display = 'flex';
  }

  function closeModal(){
    modal.style.display = 'none';
  }

  closeModalBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){
    if(e.target === modal) closeModal();
  });

  copyPixBtn.addEventListener('click', function(){
    navigator.clipboard.writeText(pixCodeText.textContent).then(()=>{
      copyPixBtn.textContent = 'Copiado!';
      setTimeout(()=> copyPixBtn.textContent = 'Copiar código PIX', 2000);
    });
  });

  payBtn.addEventListener('click', async function(){
    result.innerHTML = '';
    const ema = (emailInput.value || '').trim();
    if (!ema){
      showError('Informe seu email.');
      return;
    }
    payBtn.disabled = true;
    payBtn.textContent = 'Gerando pagamento...';

    try {
      const resp = await fetch('../config/criar_pagamento.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ preco: price, email: ema })
      });

      const data = await resp.json();

      if (!resp.ok) {
        showError(data.error || 'Erro ao criar pagamento.');
        payBtn.disabled = false;
        payBtn.textContent = 'Pagar';
        return;
      }

      if (data.qr_code_base64) {
        const imgSrc = 'data:image/png;base64,' + data.qr_code_base64;
        openModal(imgSrc, data.qr_code || '');
      } else if (data.qr_code) {
        openModal('', data.qr_code);
      } else {
        result.innerHTML = '<div>Pagamento criado. ID: '+(data.id || '')+'</div>';
      }
    } catch (err) {
      showError('Erro de conexão: ' + err.message);
    } finally {
      payBtn.disabled = false;
      payBtn.textContent = 'Pagar';
    }
  });
})();
</script>
