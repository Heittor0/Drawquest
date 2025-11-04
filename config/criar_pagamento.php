<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';



MercadoPago\SDK::setAccessToken('APP_USR-8241181758277150-110118-c2698262c1775d2af8b0598c1a779ef4-835289279');

header('Content-Type: application/json');

// ler JSON enviado
$data = json_decode(file_get_contents("php://input"), true);

$preco = isset($data['preco']) ? floatval($data['preco']) : 0.0;
$email = isset($data['email']) ? trim($data['email']) : '';

if ($preco <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Preço inválido ou não definido.']);
    exit;
}

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email do pagador não definido.']);
    exit;
}

$payment = new MercadoPago\Payment();
$payment->transaction_amount = $preco;
$payment->payment_method_id = "pix";
$payment->payer = ["email" => $email];
$payment->save();

if (empty($payment->id)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Falha ao criar pagamento.',
        'details' => $payment->error ?? null
    ]);
    exit;
}

$qr = $payment->point_of_interaction->transaction_data ?? null;

http_response_code(201);
echo json_encode([
    "id" => $payment->id,
    "status" => $payment->status ?? null,
    "qr_code" => $qr->qr_code ?? null,
    "qr_code_base64" => $qr->qr_code_base64 ?? null
]);



$sql = "SELECT id, nome, pdf, texto, imagem, preco, classe, estilo, tempodepostagem
        FROM produtos
        WHERE id = :id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$pro = $stmt->fetch(PDO::FETCH_ASSOC);




if($payment === 'aprovado'){
    if($id == 1){
    header("location:../imagens_dowloads/imagem1");
    exit;
    }
     if($id == 1){
    header("location:../imagens_dowloads/imagem1");
    exit;
    }
    
}
exit;
?>

