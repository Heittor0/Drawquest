<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';

MercadoPago\SDK::setAccessToken('APP_USR-8241181758277150-110118-c2698262c1775d2af8b0598c1a779ef4-835289279');

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['data']['id'])) {
    http_response_code(200);
    exit;
}
$payment_id = $input['data']['id'];

$payment = MercadoPago\Payment::find_by_id($payment_id);

if (!$payment) {
    http_response_code(200);
    exit;
}

if ($payment->status === 'approved') {
    $user_id = $payment->metadata->user_id ?? null;
    $produto_id = $payment->metadata->product_id ?? null;
    if ($user_id && $produto_id) {
        $stmt = $pdo->prepare("UPDATE compras SET status='aprovado', data_compra = NOW() WHERE payment_id = :pid AND user_id = :u AND produto_id = :p");
        $stmt->execute([
            ':pid' => $payment_id,
            ':u' => $user_id,
            ':p' => $produto_id
        ]);
        // gerar link/download ou marcar para liberar
        // Exemplo: inserir na tabela downloads
        $stmt2 = $pdo->prepare("INSERT IGNORE INTO downloads (user_id, produto_id, file_path, data_liberacao) VALUES (:u, :p, :fp, NOW())");
        // supondo que você guarde o caminho em produtos.imagem ou pdf
        // pegar caminho:
        $stmtP = $pdo->prepare("SELECT pdf, imagem FROM produtos WHERE id = :p LIMIT 1");
        $stmtP->execute([':p'=>$produto_id]);
        $prod = $stmtP->fetch(PDO::FETCH_ASSOC);
        $filePath = $prod['pdf'] ?: $prod['imagem'];
        $stmt2->execute([
            ':u' => $user_id,
            ':p' => $produto_id,
            ':fp' => $filePath
        ]);
    }
}

http_response_code(200);
exit;
?>
