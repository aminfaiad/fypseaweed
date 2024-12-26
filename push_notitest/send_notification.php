<?php
require 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$subscriptions = json_decode(file_get_contents('subscriptions.json'), true);

$webPush = new WebPush([
    'VAPID' => [
        'subject' => 'mailto:your-email@example.com',
        'publicKey' => 'YOUR_PUBLIC_VAPID_KEY',
        'privateKey' => 'YOUR_PRIVATE_VAPID_KEY',
    ],
]);

foreach ($subscriptions as $subscriptionData) {
    $subscription = Subscription::create([
        'endpoint' => $subscriptionData['endpoint'],
        'publicKey' => $subscriptionData['keys']['p256dh'],
        'authToken' => $subscriptionData['keys']['auth'],
    ]);

    $webPush->queueNotification($subscription, json_encode([
        'title' => 'Hello!',
        'body' => 'This is a push notification.',
        'icon' => 'icon.png',
    ]));
}

foreach ($webPush->flush() as $report) {
    if ($report->isSuccess()) {
        echo "Notification sent successfully to {$report->getEndpoint()}\n";
    } else {
        echo "Notification failed: {$report->getReason()}\n";
    }
}
