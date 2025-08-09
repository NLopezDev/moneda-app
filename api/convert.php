<?php
declare(strict_types=1);

// Headers
header('Content-Type: application/json; charset=utf-8');
// Si vas a servir front/back en dominios distintos, ajustá esto:
header('Access-Control-Allow-Origin: *');

// Config
$CACHE_DIR = __DIR__ . '/.cache';
$CACHE_TTL = 60 * 30; // 30 minutos
$ALLOWED = [
  "USD","EUR","ARS","BRL","CLP","UYU","MXN","COP","GBP","JPY","CHF","CAD",
  "AUD","CNY","HKD","INR","RUB","ZAR","KRW","SGD","NZD"
];

// Helpers
function bad_request(string $msg, int $code = 400): void {
  http_response_code($code);
  echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
  exit;
}
function safe_currency(?string $c, array $allowed): string {
  if ($c === null) bad_request("Falta parámetro de moneda.", 400);
  $c = strtoupper(trim($c));
  if (!in_array($c, $allowed, true)) bad_request("Moneda no permitida: $c", 400);
  return $c;
}
function ensure_cache_dir(string $dir): void {
  if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
  if (!is_writable($dir)) { @chmod($dir, 0775); }
}

// Input
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : null;
$from   = $_GET['from'] ?? null;
$to     = $_GET['to'] ?? null;

if ($amount === null) bad_request("Parámetro 'amount' requerido.");
if (!is_finite($amount) || $amount < 0) bad_request("El monto debe ser un número válido (≥ 0).");
$from = safe_currency($from, $ALLOWED);
$to   = safe_currency($to, $ALLOWED);

ensure_cache_dir($CACHE_DIR);

// Cache por par (from->to)
$cacheKey = sprintf('%s_%s.json', $from, $to);
$cacheFile = $CACHE_DIR . '/' . $cacheKey;

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $CACHE_TTL) {
  $payload = json_decode((string)file_get_contents($cacheFile), true);
} else {
  // Proveedor gratuito alternativo
  $url = sprintf('https://open.er-api.com/v6/latest/%s',
    urlencode($from)
  );
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
  ]);
  $raw = curl_exec($ch);
  $err = curl_error($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($raw === false || $code >= 400) {
    bad_request("Fallo al obtener tasa ($code) " . ($err ?: ''), 502);
  }

  $json = json_decode($raw, true);
  if (!isset($json['rates'][$to])) {
    bad_request("Respuesta inválida del proveedor.", 502);
  }

  $rate = (float)$json['rates'][$to];
  $payload = [
    'rate' => $rate,
    'provider' => 'exchangerate-api.com',
    'ts' => (int) (microtime(true) * 1000)
  ];

  @file_put_contents($cacheFile, json_encode($payload, JSON_UNESCAPED_UNICODE));
}

$converted = $amount * (float)$payload['rate'];

echo json_encode([
  'converted' => $converted,
  'rate' => (float)$payload['rate'],
  'provider' => $payload['provider'] ?? 'unknown',
  'ts' => $payload['ts'] ?? (int)(microtime(true) * 1000),
], JSON_UNESCAPED_UNICODE);