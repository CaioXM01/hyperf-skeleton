<?php

namespace App\Domain\Services\Notification;

interface NotificationServiceInterface
{
    /**
     * Envie uma notificação para a transação especificada.
     *
     * @return bool True se a notificação for enviada com sucesso, false caso contrário.
     */
    public function sendNotification(): bool;
}
