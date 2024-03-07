<?php

namespace App\Domain\HttpClients;

interface NotificationClientInterface
{
    /**
     * Envia uma notificação.
     *
     * @return array
     * @throws \Exception
     */
    public function sendNotification(): array;
}
