<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';

// ⚠️ USE o access token de PRODUÇÃO para testes reais com app bancário
MercadoPago\SDK::setAccessToken('APP_USR-8241181758277150-110118-c2698262c1775d2af8b0598c1a779ef4-835289279'); // substitua depois por PROD

header('Content-Type: application/json');

// Ler dados recebidos
$input = json_decode(file_get_contents("php://input"), true);
$preco = isset($input['preco']) ? floatval($input['preco']) : 0;
$email = isset($input['email']) ? trim($input['email']) : '';

if ($preco <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Preço inválido ou não definido.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'E-mail inválido.']);
    exit;
}

try {
    $payment = new MercadoPago\Payment();
    $payment->transaction_amount = round($preco, 2); // valor exato
    $payment->description = "Compra via PIX - DrawQuest";
    $payment->payment_method_id = "pix";
    $payment->payer = ["email" => $email];
    $payment->notification_url = "https://seusite.com.br/webhook_pix.php"; // opcional

    $payment->save();

    if (!empty($payment->error)) {
        throw new Exception(json_encode($payment->error));
    }

    $qr = $payment->point_of_interaction->transaction_data ?? null;

    if (!$qr || empty($qr->qr_code)) {
        throw new Exception('Erro ao gerar QR Code PIX.');
    }

    http_response_code(201);
    echo json_encode([
        "id" => $payment->id,
        "status" => $payment->status,
        "qr_code" => $qr->qr_code,
        "qr_code_base64" => $qr->qr_code_base64,
        "ticket_url" => $qr->ticket_url ?? null
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Falha ao criar pagamento PIX.",
        "details" => $e->getMessage()
    ]);
}
?>
