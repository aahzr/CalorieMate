<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class FatSecretService
{
    protected $client;
    protected $consumerKey;
    protected $consumerSecret;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->consumerKey = config('services.fatsecret.consumer_key');
        $this->consumerSecret = config('services.fatsecret.consumer_secret');
        $this->apiUrl = config('services.fatsecret.api_url');

        if (empty($this->consumerKey) || empty($this->consumerSecret) || empty($this->apiUrl)) {
            throw new \Exception('FatSecret API configuration is missing in services.fatsecret');
        }
    }

    public function searchFood($query, $maxResults = 10, $market = 'US')
    {
        $params = [
            'method' => 'foods.search',
            'search_expression' => $query,
            'max_results' => $maxResults,
            'market' => $market,
            'format' => 'json',
        ];

        \Log::info('FatSecret searchFood query: ' . $query . ', params: ' . json_encode($params));

        try {
            $response = $this->makeSignedRequest($params);
            $results = $response['foods']['food'] ?? [];
            \Log::info('FatSecret searchFood response: ' . json_encode($results));
            return $results;
        } catch (\Exception $e) {
            \Log::error('FatSecret searchFood error: ' . $e->getMessage());
            return [];
        }
    }

    public function getFoodById($foodId)
    {
        $params = [
            'method' => 'food.get.v4',
            'food_id' => $foodId,
            'format' => 'json',
        ];

        \Log::info('FatSecret getFoodById food_id: ' . $foodId);

        try {
            $response = $this->makeSignedRequest($params);
            if (empty($response['food']) || empty($response['food']['servings']['serving'])) {
                \Log::warning('FatSecret getFoodById: Invalid or empty response for food_id=' . $foodId . ', response=' . json_encode($response));
                return [];
            }
            $result = $response['food'];
            \Log::info('FatSecret getFoodById response: ' . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            \Log::error('FatSecret getFoodById error: ' . $e->getMessage());
            return [];
        }
    }

    protected function makeSignedRequest(array $params)
    {
        $oauthParams = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_nonce' => Str::random(10),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
        ];

        $params = array_merge($params, $oauthParams);
        ksort($params);

        $baseString = 'GET&' . rawurlencode($this->apiUrl) . '&' . rawurlencode(http_build_query($params, '', '&', PHP_QUERY_RFC3986));
        $signature = base64_encode(hash_hmac('sha1', $baseString, $this->consumerSecret . '&', true));
        $params['oauth_signature'] = $signature;

        try {
            $response = $this->client->get($this->apiUrl, [
                'query' => $params,
                'verify' => false, // Nonaktifkan verifikasi SSL untuk lokal
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to make FatSecret API request: ' . $e->getMessage());
        }
    }
}