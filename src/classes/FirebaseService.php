<?php

declare(strict_types=1);
require_once __DIR__ . '/../config/FirebaseConfig.php';


class FirebaseService
{
    private string $accessToken;
    private int $tokenExpiry;

    public function __construct()
    {
        $this->generateAccessToken();
    }

    // Returns valid access token, regenerates if expired
    public function getAccessToken(): string
    {
        if (empty($this->accessToken) || time() >= $this->tokenExpiry) {
            $this->generateAccessToken();
        }
        return $this->accessToken;
    }

    // Generate OAuth2 Bearer token using Service Account JSON
    private function generateAccessToken(): void
    {
        if (!file_exists(FirebaseConfig::SERVICE_ACCOUNT_FILE)) {
            throw new RuntimeException('Service account file not found.');
        }

        $json = json_decode(file_get_contents(FirebaseConfig::SERVICE_ACCOUNT_FILE), true);
        if (!isset($json['client_email'], $json['private_key'])) {
            throw new RuntimeException('Invalid service account JSON.');
        }

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $now = time();
        $claim = [
            'iss' => $json['client_email'],
            'scope' => FirebaseConfig::FCM_SCOPE,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
        ];

        $jwtHeader = $this->base64UrlEncode(json_encode($header));
        $jwtClaim  = $this->base64UrlEncode(json_encode($claim));

        $privateKey = str_replace('\\n', "\n", $json['private_key']);
        $key = openssl_pkey_get_private($privateKey);
        if (!$key) {
            throw new RuntimeException('Invalid private key in service account.');
        }

        openssl_sign("$jwtHeader.$jwtClaim", $signature, $key, 'SHA256');
        $jwt = "$jwtHeader.$jwtClaim." . $this->base64UrlEncode($signature);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]),
        ]);

        $res = curl_exec($ch);
        if (!$res) {
            throw new RuntimeException('Token request failed: ' . curl_error($ch));
        }
        curl_close($ch);

        $response = json_decode($res, true);
        if (!isset($response['access_token'])) {
            throw new RuntimeException('Access token not returned by Google.');
        }

        $this->accessToken = $response['access_token'];
        $this->tokenExpiry = $now + 3500;
    }

    // Base64 URL encode
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
