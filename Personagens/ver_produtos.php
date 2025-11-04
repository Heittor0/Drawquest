<?php
session_start();
require '../config/config.php';
$jaComprou = false;

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

// trata imagem (corrige caminho local → URL acessível)
$placeholder = 'https://i.imgur.com/DMXG4nK.png';
$img = $placeholder;

if (!empty($pro['imagem']) && is_string($pro['imagem'])) {
    $imagem = $pro['imagem'];

    // Se for caminho absoluto do Windows, converte
    if (preg_match('/^[A-Z]:\\\\/i', $imagem)) {
        // Remove o prefixo do XAMPP
        $imagem = str_replace(['C:\\xampp\\htdocs\\Site-RPG-xamp\\', '\\'], ['../', '/'], $imagem);
        $img = $imagem;
    } elseif (strpos($imagem, 'http') === 0) {
        // Caminho remoto
        $img = $imagem;
    } else {
        // Caminho relativo normal
        $img = '../' . ltrim($imagem, '/');
    }
}

// obter email do usuário (sessão)
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
 $userEmail = $_SESSION['email'] ?? null;
  $produto_id = $pro['id'];

  $sql = "SELECT * FROM pagamentos WHERE email = ? AND produto_id = ? AND status = 'pago'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userEmail, $produto_id]);
  $jaComprou = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pro['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8') ?> — DrawQuest</title>
    <link rel="stylesheet" href="StylePerso.css?v=1.0.4">
    <style>
      
/* ======== ESTILO BLOMBÔ ADAPTADO PARA DRAQQUEST ======== */
body {
  background-color: #0d1b2a;
  color: #e0e1dd;
  font-family: "Poppins", sans-serif;
  margin: 0;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Cabeçalho padrão mantido */
header {
  background-color: #1b263b;
  padding: 20px 10%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #ffb70322;
}
header h1 {
  color: #ffd166;
  font-size: 1.6rem;
  margin: 0;
}
nav a {
  color: #e0e1dd;
  text-decoration: none;
  font-weight: 500;
  margin-left: 20px;
  transition: color 0.3s ease;
}
nav a:hover {
  color: #ffb703;
}

/* ======== SEÇÃO PRINCIPAL ======== */
main.personagens {
  flex: 1;
  padding: 80px 10%;
  display: flex;
  justify-content: center;
}

.card-single {
  display: grid;
  grid-template-columns: 1fr 1fr;
  background-color: #1b263b;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 6px 25px rgba(0,0,0,0.4);
  max-width: 1000px;
  width: 100%;
  transition: transform 0.3s ease;
}
.card-single:hover {
  transform: translateY(-3px);
}

/* ======== IMAGEM ======== */
.card-single img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* ======== TEXTO E INFO ======== */
.card-single .content {
  padding: 50px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: left;
}

.card-single h2 {
  font-size: 2.3rem;
  color: #ffd166;
  margin-bottom: 20px;
}

.price-tag {
  font-size: 1.6rem;
  color: #ffb703;
  font-weight: bold;
  margin-bottom: 25px;
}

.description {
  font-size: 1.05rem;
  line-height: 1.7;
  color: #e0e1dd;
  margin-bottom: 40px;
}

/* ======== BOTÃO DE PAGAMENTO ======== */
#payment h3 {
  margin-bottom: 15px;
  color: #ffd166;
  font-size: 1.3rem;
}

#payment button {
  background-color: #ffb703;
  color: #0d1b2a;
  padding: 14px 30px;
  font-size: 1.1rem;
  font-weight: bold;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}
#payment button:hover {
  background-color: #ffd166;
  transform: translateY(-2px);
}
#payment button:disabled {
  background-color: #888;
  cursor: not-allowed;
}

/* ======== MODAL PIX ======== */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.75);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-content {
  background-color: #1b263b;
  color: #e0e1dd;
  padding: 30px;
  border-radius: 12px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  position: relative;
  box-shadow: 0 8px 20px rgba(0,0,0,0.5);
}

.modal-content .close-modal {
  position: absolute;
  top: 10px;
  right: 12px;
  background: transparent;
  border: none;
  color: #ffd166;
  font-size: 1.5rem;
  cursor: pointer;
}

.modal-content img {
  max-width: 180px;
  width: 50%;
  margin: 20px auto 10px;
  display: block;
}

.modal-content pre {
  background-color: #0d1b2a;
  color: #ffd166;
  padding: 12px;
  border-radius: 6px;
  font-family: monospace;
  text-align: left;
  white-space: pre-wrap;
  word-break: break-all;
  margin: 10px 0;
}

.modal-content button.copy-pix {
  background-color: #ffd166;
  color: #0d1b2a;
  border: none;
  padding: 10px 22px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.modal-content button.copy-pix:hover {
  background-color: #ffb703;
}

/* ======== RODAPÉ ======== */
footer {
  background-color: #0d1b2a;
  border-top: 1px solid #ffb70333;
  text-align: center;
  padding: 25px;
  color: #ccc;
  font-size: 0.9rem;
}

/* ======== RESPONSIVIDADE ======== */
@media (max-width: 900px) {
  .card-single {
    grid-template-columns: 1fr;
  }
  .card-single .content {
    padding: 40px 30px;
    text-align: center;
  }
  .card-single h2 {
    font-size: 2rem;
  }
  .price-tag {
    font-size: 1.3rem;
  }
}


    </style>
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

      <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" 
           alt="Imagem de <?= htmlspecialchars($pro['nome'], ENT_QUOTES, 'UTF-8') ?>" 
           style="max-width:300px; border-radius:10px; display:block; margin:1rem auto;">

      <p class="price-tag">
        <strong>Preço:</strong> <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '-' ?>
      </p>

      <p class="description">
        <strong>Classe:</strong> <?= htmlspecialchars($pro['classe'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
        <strong>Raça:</strong> <?= htmlspecialchars($pro['estilo'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
        <?= nl2br(htmlspecialchars($pro['texto'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
      </p>

     

      <?php if(empty($_SESSION['id'])): ?>
        <p style="color:#b00;">⚠️ O pagamento só é habilitado quando logado.</p>
      <?php else: ?>
        <div id="payment">
          <h3>Pagar este produto</h3>
          <button id="payBtn" <?= empty($pro['preco']) ? 'disabled' : '' ?>>
            Pagar <?= isset($pro['preco']) ? 'R$ ' . number_format((float)$pro['preco'], 2, ',', '.') : '' ?>
          </button>
          <div id="paymentResult" style="margin-top:1rem;"></div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Modal PIX -->
  <div id="qrModal" class="modal-overlay" style="display:none;">
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
  const userEmail = <?= json_encode($userEmail) ?>;
  const result = document.getElementById('paymentResult');
  const modal = document.getElementById('qrModal');
  const modalImg = document.getElementById('qrModalImg');
  const pixCodeText = document.getElementById('pixCodeText');
  const copyPixBtn = modal.querySelector('button.copy-pix');
  const closeModalBtn = modal.querySelector('button.close-modal');

  // local onde vamos inserir o link (abaixo da descrição)
  const description = document.querySelector('.description');

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

  payBtn?.addEventListener('click', async function(){
    result.innerHTML = '';

    const ema = userEmail;
    if (!ema){
      showError('Sua sessão não retornou um email válido.');
      return;
    }

    payBtn.disabled = true;
    payBtn.textContent = 'Gerando pagamento...';

    try {
      const resp = await fetch('../config/criar_pagamento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ preco: price, email: ema })
      });

      const data = await resp.json();

      if (!resp.ok) {
        showError(data.error || 'Erro ao criar pagamento.');
        return;
      }

      // Mostra QR Code PIX
      if (data.qr_code_base64 || data.qr_code) {
        const imgSrc = data.qr_code_base64
          ? 'data:image/png;base64,' + data.qr_code_base64
          : '';
        openModal(imgSrc, data.qr_code || '');

        // Exibe o botão para baixar imagem do produto (apenas depois de clicar em pagar)
        const imgDownload = document.createElement('p');
        imgDownload.innerHTML = `
          <a href="../config/baixar_imagem.php?id=<?= (int)$pro['id'] ?>"
             style="color:#ffd166;font-weight:bold;display:inline-block;margin-top:15px;">
             🖼️ Baixar imagem do produto
          </a>`;
        description.insertAdjacentElement('afterend', imgDownload);
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
