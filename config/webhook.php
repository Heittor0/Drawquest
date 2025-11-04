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

if ($payment->status === "approved") {

    $user_id = $payment->metadata->user_id ?? null;
    $product_id = $payment->metadata->product_id ?? null;

    if ($user_id && $product_id) {

        $stmt = $pdo->prepare("UPDATE compras
            SET status='aprovado'
            WHERE user_id=:u AND produto_id=:p");
        $stmt->execute([
            ':u' => $user_id,
            ':p' => $product_id
        ]);
    }
}

http_response_code(200);
exit;
?>
