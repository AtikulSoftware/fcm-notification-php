<?php

declare(strict_types=1);
class FirebaseConfig
{
    public const SERVICE_ACCOUNT_FILE = __DIR__ . '/service-account.json';
    public const PROJECT_ID = 'mynotifications-dd110';
    public const FCM_SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    public const FCM_URL = 'https://fcm.googleapis.com/v1/projects/' . self::PROJECT_ID . '/messages:send';
}
