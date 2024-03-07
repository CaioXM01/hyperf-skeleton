<?php

namespace App\Domain\Services\Notification;

use App\Domain\HttpClients\NotificationClientInterface;

class NotificationService implements NotificationServiceInterface
{
    /**
     * @Inject
     * @var NotificationClientInterface
     */
    protected $notificationClient;

    public function __construct(
        NotificationClientInterface $notificationClient
    ) {
        $this->notificationClient = $notificationClient;
    }

    public function sendNotification(): bool
    {
        $notification = $this->notificationClient->sendNotification();
        echo "\n", $notification['message'];
        if ($notification['message'] === true) {
            echo "\n deu bom";
            return true;
        }
        echo "\n", "Notification not sent, notification service may be unavailable or unstable.";
        return false;
    }
}
