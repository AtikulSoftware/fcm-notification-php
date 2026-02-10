<?php

declare(strict_types=1);
require_once __DIR__ . '/FirebaseService.php';
require_once __DIR__ . '/../config/FirebaseConfig.php';

// Sends Firebase Cloud Messaging notification
class FirebaseNotification
{
    private string $deviceToken;
    private string $title;
    private string $body;
    private array $data;
    private FirebaseService $firebaseService;

    public function __construct(
        string $deviceToken,
        string $title,
        string $body,
        array $data = []
    ) {
        $this->deviceToken = $deviceToken;
        $this->title = $title;
        $this->body = $body;
        $this->data = $this->sanitizeData($data);
        $this->firebaseService = new FirebaseService();
    }

    // Ensure all data values are strings
    private function sanitizeData(array $data): array
    {
        return array_map('strval', $data);
    }

    // Send the notification
    public function send(): array
    {
        $payload = [
            'message' => [
                'token' => $this->deviceToken,
                'notification' => [
                    'title' => $this->title,
                    'body'  => $this->body
                ],
                'data' => $this->data
            ]
        ];

        $ch = curl_init(FirebaseConfig::FCM_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->firebaseService->getAccessToken(),
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => $httpCode === 200,
            'status_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }
}
