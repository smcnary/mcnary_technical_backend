<?php

// Test script to verify OpenPhone API connection
// Run with: php test_openphone.php

require_once 'vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;

$apiKey = 'U4patvgjXq2BUyLeRm8cgF4Eo3ciFuid';
$baseUrl = 'https://api.openphone.com/v1';

echo "Testing OpenPhone API connection...\n";
echo "API Key: " . substr($apiKey, 0, 8) . "...\n";
echo "Base URL: $baseUrl\n\n";

// Test different authentication methods
$authMethods = [
    'Bearer Token' => ['Authorization' => 'Bearer ' . $apiKey],
    'API Key Header' => ['X-API-Key' => $apiKey],
    'Authorization Basic' => ['Authorization' => 'Basic ' . base64_encode($apiKey . ':')],
];

foreach ($authMethods as $methodName => $headers) {
    echo "Testing $methodName...\n";
    
    $httpClient = HttpClient::create([
        'headers' => array_merge([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $headers),
    ]);

    try {
        $response = $httpClient->request('GET', $baseUrl . '/phone-numbers');
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 200) {
            $data = $response->toArray();
            echo "✅ Success with $methodName! Found " . count($data['data'] ?? []) . " phone numbers\n";
            
            if (!empty($data['data'])) {
                echo "Available phone numbers:\n";
                foreach ($data['data'] as $phone) {
                    echo "  - " . ($phone['displayName'] ?? $phone['phoneNumber']) . " (" . $phone['phoneNumber'] . ")\n";
                }
            }
            break; // Stop testing if we found a working method
        } else {
            echo "❌ $methodName failed with status code: $statusCode\n";
            $content = $response->getContent(false);
            if (strlen($content) < 500) {
                echo "Response: $content\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ $methodName error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test account info endpoint
echo "Testing account info endpoint...\n";
$httpClient = HttpClient::create([
    'headers' => [
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ],
]);

try {
    $response = $httpClient->request('GET', $baseUrl . '/account');
    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 200) {
        $data = $response->toArray();
        echo "✅ Account info retrieved successfully!\n";
        echo "Account: " . ($data['name'] ?? 'Unknown') . "\n";
    } else {
        echo "❌ Account info failed with status code: $statusCode\n";
        echo "Response: " . $response->getContent(false) . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Account info error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
