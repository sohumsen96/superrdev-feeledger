<?php
/*
 * Send a signed test webhook to FeeLedger, the way the payment gateway does.
 *
 *   php tools/send_test_webhook.php [base_url] [student_id] [amount_in_paise] [--bad-signature]
 *   php tools/send_test_webhook.php http://localhost:8000/index.php/ 7 350000
 */
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/config/feeledger.php';

$args = array_values(array_filter(array_slice($argv, 1), function ($a) { return $a !== '--bad-signature'; }));
$bad = in_array('--bad-signature', $argv, TRUE);
$base = isset($args[0]) ? rtrim($args[0], '/') . '/' : 'http://localhost:8000/index.php/';
$student_id = isset($args[1]) ? (int) $args[1] : 1;
$amount = isset($args[2]) ? (int) $args[2] : 350000;

$body = json_encode(array(
	'event'   => 'payment.captured',
	'payment' => array(
		'id'          => 'pay_test_' . bin2hex(random_bytes(6)),
		'amount'      => $amount,
		'currency'    => 'INR',
		'method'      => 'upi',
		'captured_at' => time(),
		'notes'       => array('student_id' => $student_id),
	),
));
$signature = hash_hmac('sha256', $body, $bad ? 'wrong-secret' : $config['webhook_secret']);

$ch = curl_init($base . 'payments/webhook');
curl_setopt_array($ch, array(
	CURLOPT_POST => TRUE,
	CURLOPT_POSTFIELDS => $body,
	CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'X-FeeLedger-Signature: ' . $signature),
	CURLOPT_RETURNTRANSFER => TRUE,
));
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

echo "HTTP $status " . trim((string) $response) . "\n";
exit($status === 200 ? 0 : 1);
