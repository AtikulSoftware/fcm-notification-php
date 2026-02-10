<?php

declare(strict_types=1);
require_once __DIR__ . '/classes/FirebaseNotification.php';

$deviceToken = 'foJU71W-SoO23sSyuMam1M:APA91bHI5mQxEBBz3c1ZBFUXk4CptwDuNb1ICFPLCcr-FMbAUFqKJC4ldjmDs5jdcvJtac0APR8Ez5JFrx_YUQO1PU1x8z5OIspVyZa-vTnbpqp_BYSLFc4';
$notification = new FirebaseNotification(
    $deviceToken,
    'Hello 🔔',
    'This is a clean OOP Firebase Notification!',
    [
        'type' => 'message',
        'user_id' => 101
    ]
);

$result = $notification->send();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
