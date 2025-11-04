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

  <style>
    .carrossel-fade {
      text-align: center;
      padding: 60px 0;
      background-color: #16213e;
    }

    .carrossel-container {
      position: relative;
      width: 80%;
      max-width: 600px;
      margin: 0 auto;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
    }

    .slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      opacity: 0;
      transition: opacity 1s ease;
    }

    .slide.ativo {
      opacity: 1;
      position: relative;
    }

    .slide img {
  width: 100%;
  height: auto;
  max-height: 400px; /* limite opcional */
  object-fit: contain; /* exibe a imagem inteira */
  border-radius: 20px;
  background-color: #0d1b2a; /* cor de fundo para imagens menores */
}


    .legenda {
      position: absolute;
      bottom: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.5);
      color: #fff;
      padding: 10px;
      font-size: 1rem;
      border-radius: 0 0 20px 20px;
    }

    .btn-seta {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0,0,0,0.4);
      color: #fff;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      padding: 8px 12px;
      border-radius: 50%;
      transition: background 0.3s;
    }

    .btn-seta:hover {
      background: rgba(0,0,0,0.7);
    }

    .btn-seta.esquerda { left: 10px; }
    .btn-seta.direita { right: 10px; }

    .indicadores {
      display: flex;
      justify-content: center;
      margin-top: 15px;
      gap: 8px;
    }

    .indicadores button {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      border: none;
      background-color: #fff4;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .indicadores button.ativo {
      background-color: #fff;
    }
  </style>
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

  

  <section class="herois">
    <h2>Dê vida à sua aventura com personagens únicos e prontos para jogar!</h2>
    <p>Arte original, estilos variados e fichas completas para seus mundos de RPG.</p>
    <a href="#carrossel" class="btn">Ver personagens</a>
  </section>

  <!-- 🎠 Carrossel de imagens locais -->
  <section id="carrossel" class="carrossel-fade">
    <h3>Galeria de Personagens</h3>
    <div class="carrossel-container">

      <div class="slide ativo">
        <img src="../imagens/imagens_carrosel/01_07_25.png" alt="Imagem 1">
        <p class="legenda">Imagem 1</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/02_07_25.png" alt="Imagem 2">
        <p class="legenda">Imagem 2</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/17_07_25.png" alt="Imagem 3">
        <p class="legenda">Imagem 3</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/27_07_25.png" alt="Imagem 4">
        <p class="legenda">Imagem 4</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/Alyssa.png" alt="Imagem 5">
        <p class="legenda">Imagem 5</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/grupo.png" alt="Imagem 6">
        <p class="legenda">Imagem 6</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/HiPaint_1760650165362.png" alt="Imagem 7">
        <p class="legenda">Imagem 7</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/TheJaoJao.png" alt="Imagem 8">
        <p class="legenda">Imagem 8</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/Shizumi.png" alt="Imagem 9">
        <p class="legenda">Imagem 9</p>
      </div>

      <div class="slide">
        <img src="../imagens/imagens_carrosel/Thalëriann.png" alt="Imagem 10">
        <p class="legenda">Imagem 10</p>
      </div>

      <button class="btn-seta esquerda">◀</button>
      <button class="btn-seta direita">▶</button>
    </div>

    <div class="indicadores"></div>
  </section>

  <footer>
    © 2025 DrawQuest — Crie, jogue e viva suas histórias!
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const slides = document.querySelectorAll('.slide');
      const esquerda = document.querySelector('.btn-seta.esquerda');
      const direita = document.querySelector('.btn-seta.direita');
      const indicadores = document.querySelector('.indicadores');
      let indice = 0;

      slides.forEach((_, i) => {
        const btn = document.createElement('button');
        if (i === 0) btn.classList.add('ativo');
        btn.addEventListener('click', () => mudarSlide(i));
        indicadores.appendChild(btn);
      });

      const atualizar = () => {
        slides.forEach((s, i) => s.classList.toggle('ativo', i === indice));
        indicadores.querySelectorAll('button').forEach((b, i) => b.classList.toggle('ativo', i === indice));
      };

      const mudarSlide = (novo) => {
        indice = (novo + slides.length) % slides.length;
        atualizar();
      };

      esquerda.addEventListener('click', () => mudarSlide(indice - 1));
      direita.addEventListener('click', () => mudarSlide(indice + 1));

      // Troca automática
      setInterval(() => mudarSlide(indice + 1), 5000);
    });
  </script>

</body>
</html>
