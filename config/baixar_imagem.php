<?php
session_start();
require "../config/config.php"; // mesmo arquivo que você usa nas outras páginas

// Verifica se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido ou não informado.");
}

$id = (int) $_GET['id'];

// Busca o arquivo PDF no banco de dados
$sql = "SELECT pdf FROM produtos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arquivo || empty($arquivo['pdf'])) {
    die("Arquivo não encontrado no banco de dados.");
}

$caminhoArquivo = $arquivo['pdf'];

// Verifica se o arquivo existe fisicamente
if (!file_exists($caminhoArquivo)) {
    die("O arquivo não foi encontrado no servidor: " . htmlspecialchars($caminhoArquivo));
}

// Força o download do arquivo
$nomeArquivo = basename($caminhoArquivo);
$tipoMime = mime_content_type($caminhoArquivo);

header("Content-Type: $tipoMime");
header("Content-Disposition: attachment; filename=\"$nomeArquivo\"");
header("Content-Length: " . filesize($caminhoArquivo));
readfile($caminhoArquivo);
exit;
?>
