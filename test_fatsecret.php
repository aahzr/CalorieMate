<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$consumerKey = '05e0222316a24a8885220a5fea7e8057'; // Ganti dengan kredensial Anda
$consumerSecret = 'c29af31c4ccf4faf8fa65d1c1c4467a3'; // Ganti dengan kredensial Anda
$apiUrl = 'https://platform.fatsecret.com/rest/server.api';

$client = new Client();
$query = 'ayam'; // Coba kueri umum
$maxResults = 10;
$market = 'IDN';

$params = [
    'method' => 'foods.search',
    'search_expression' => $query,
    'max_results' => $maxResults,
    'market' => $market,
    'format' => 'json',
    'oauth_consumer_key' => $consumerKey,
    'oauth_nonce' => bin2hex(random_bytes(5)),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_version' => '1.0',
];

ksort($params);
$baseString = 'GET&' . rawurlencode($apiUrl) . '&' . rawurlencode(http_build_query($params, '', '&', PHP_QUERY_RFC3986));
$signature = base64_encode(hash_hmac('sha1', $baseString, $consumerSecret . '&', true));
$params['oauth_signature'] = $signature;

try {
    $response = $client->get($apiUrl, [
        'query' => $params,
        'verify' => false, // Nonaktifkan SSL untuk lokal
    ]);
    $data = json_decode($response->getBody()->getContents(), true);
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
} catch (GuzzleException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>