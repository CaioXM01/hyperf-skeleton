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
        if ($notification['message'] === true) {
            return true;
        }
        return false;
    }
}
