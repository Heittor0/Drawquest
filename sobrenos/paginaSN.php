<?php
session_start();
require "../config/config.php";





?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sobre Nós — DrawQuest</title>
  <link rel="stylesheet" href="styleSobre.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <h1>DrawQuest</h1>
    <nav>
        <?php if (empty($_SESSION['id'])): ?>
-        <a class="aba-link" href="../PaginaLogin/login.php">Login</a>
      <?php else: ?>
        <a href="../config/sair.php">Deslogar</a>
      <?php endif; ?>
      <a href="../Home/index.php">Home</a>
      
      <a href="../Personagens/paginaPerso.php">Personagens</a>
      <a href="Contato/contato.php">Contato</a>
    </nav>
  </header>

  <main class="sobre-nos">
    <section class="descricao">
      <h2>Sobre Nós</h2>
      <p>
        Bem-vindo à DrawQuest! Somos três amigos unidos pela paixão pelo RPG e pela criatividade. A ideia deste projeto surgiu da Giovana, ilustradora talentosa e fã dedicada do universo de Tormenta. Movida pela dificuldade de encontrar artes prontas e de qualidade para personagens de RPG, ela propôs a criação de um espaço acessível e inspirador para jogadores e mestres.
      </p>
      <p>
        A partir dessa proposta, Miriã (eu!) e Heittor se juntaram à Giovana para desenvolver a base do site e tornar essa ideia realidade. Enquanto Giovana se dedica à criação das ilustrações que dão vida a elfos, guerreiros, magos e outras figuras épicas, nós cuidamos da estrutura e da organização da plataforma.
      </p>
      <p>
        Nosso objetivo é oferecer artes digitais prontas para uso em fichas, tokens, campanhas e outros materiais relacionados ao RPG, facilitando a imersão e enriquecendo a experiência de jogo. Seja bem-vindo! Esperamos que aqui você encontre o visual ideal para dar vida ao seu personagem.
      </p>
    </section>

    <section class="criadores">
      <h2>Criadores</h2>
      <ul>
        <li><strong>Giovana:</strong> Ilustradora, transforma ideias em personagens épicos.</li>
        <li><strong>Miriã:</strong> Organização e visão estratégica para dar vida ao site.</li>
        <li><strong>Heittor:</strong> Responsável pela estrutura técnica e funcionamento da plataforma.</li>
      </ul>
    </section>
  </main>

  <footer>
    © 2025 DrawQuest — Crie, jogue e viva suas histórias!
  </footer>
</body>
</html>
