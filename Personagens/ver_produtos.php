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
<?php var_dump($pro); ?>

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

      <!-- Payment UI -->
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
    </div>
  </main>

  <footer>© 2025 DrawQuest</footer>

<script>
(function(){
  const price = Number(<?= json_encode($pro['preco']) ?>);
  const email = string(<?= json_encode($pro['email'])?>);

  const payBtn = document.getElementById('payBtn');
  const emailInput = document.getElementById('payerEmail');
  const result = document.getElementById('paymentResult');

  function showError(msg){
    result.innerHTML = '<div style="color:#b00;">'+msg+'</div>';
  }

  payBtn.addEventListener('click', async function(){
    result.innerHTML = '';
    const email = (email.value || '').trim();
    if (!email){
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
        body: JSON.stringify({ preco: price, email: email })
      });

      const data = await resp.json();

      if (!resp.ok) {
        showError(data.error || 'Erro ao criar pagamento.');
        payBtn.disabled = false;
        payBtn.textContent = 'Pagar';
        return;
      }

      // exibir QR Code (base64) se disponível
      if (data.qr_code_base64) {
        const img = document.createElement('img');
        img.src = 'data:image/png;base64,' + data.qr_code_base64;
        img.alt = 'QR Code PIX';
        img.style.maxWidth = '280px';
        img.style.display = 'block';
        img.style.marginTop = '0.5rem';

        const codePre = document.createElement('pre');
        codePre.textContent = data.qr_code || '';
        codePre.style.whiteSpace = 'pre-wrap';
        codePre.style.wordBreak = 'break-all';
        codePre.style.background = '#f7f7f7';
        codePre.style.padding = '8px';
        codePre.style.marginTop = '0.5rem';

        const copyBtn = document.createElement('button');
        copyBtn.textContent = 'Copiar código PIX';
        copyBtn.style.display = 'inline-block';
        copyBtn.style.marginTop = '0.5rem';
        copyBtn.addEventListener('click', function(){
          navigator.clipboard.writeText(data.qr_code || '').then(()=> {
            copyBtn.textContent = 'Copiado';
            setTimeout(()=> copyBtn.textContent = 'Copiar código PIX', 2000);
          });
        });

        result.innerHTML = '<div>Pague com o QR Code abaixo ou copie o código PIX:</div>';
        result.appendChild(img);
        result.appendChild(codePre);
        result.appendChild(copyBtn);
      } else if (data.qr_code) {
        result.innerHTML = '<div>PIX code:</div><pre style="white-space:pre-wrap;">'+data.qr_code+'</pre>';
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
</body>
</html>