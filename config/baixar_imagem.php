<?php
session_start();
require "../config/config.php";

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido ou não informado.");
}

$id = (int) $_GET['id'];

// Busca o campo 'pdf' (ou imagem de download) no banco de dados
$sql = "SELECT pdf FROM produtos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arquivo || empty($arquivo['pdf'])) {
    die("Arquivo não encontrado no banco de dados.");
}

$caminhoArquivo = trim($arquivo['pdf']);

// 🧩 Corrige caminho absoluto do Windows → caminho relativo do servidor
if (preg_match('/^[A-Z]:\\\\/i', $caminhoArquivo)) {
    // Remove o prefixo do XAMPP
    $caminhoArquivo = str_replace(
        ['C:\\xampp\\htdocs\\Site-RPG-xamp\\', '\\'],
        ['', '/'],
        $caminhoArquivo
    );
}

// 🧭 Define o caminho completo no servidor (Render)
$baseDir = __DIR__ . '/../'; // volta uma pasta
$caminhoArquivo = $baseDir . ltrim($caminhoArquivo, '/');

// Se o arquivo não existir localmente, tenta o diretório "files"
if (!file_exists($caminhoArquivo)) {
    $altPath = $baseDir . 'files/' . basename($caminhoArquivo);
    if (file_exists($altPath)) {
        $caminhoArquivo = $altPath;
    } else {
        http_response_code(404);
        die("❌ O arquivo não foi encontrado no servidor: " . htmlspecialchars($caminhoArquivo));
    }
}

// Força o download
$nomeArquivo = basename($caminhoArquivo);
$tipoMime = mime_content_type($caminhoArquivo);

header("Content-Type: $tipoMime");
header("Content-Disposition: attachment; filename=\"$nomeArquivo\"");
header("Content-Length: " . filesize($caminhoArquivo));
readfile($caminhoArquivo);
exit;
?>
