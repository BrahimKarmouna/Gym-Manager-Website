<?php

// Script to test assistant authentication

// Step 1: Create test assistant and get token
$ch = curl_init('http://127.0.0.1:8000/assistant-direct-login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Direct login response:\n";
echo $response . "\n\n";

$data = json_decode($response, true);
$token = $data['token'] ?? null;

if (!$token) {
    die("Failed to get authentication token.\n");
}

echo "Authentication token: $token\n\n";

// Step 2: Test /api/assistant/me endpoint with the token
$ch = curl_init('http://127.0.0.1:8000/api/assistant/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
$response = curl_exec($ch);
curl_close($ch);

echo "Assistant /me endpoint response:\n";
echo $response . "\n\n";

// Step 3: Test assistant login endpoint directly
$loginData = json_encode([
    'email' => 'test.assistant@example.com',
    'password' => 'password123'
]);

$ch = curl_init('http://127.0.0.1:8000/api/assistant/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
curl_close($ch);

echo "Login endpoint response:\n";
echo $response . "\n";
