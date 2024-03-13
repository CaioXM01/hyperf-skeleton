<?php

declare(strict_types=1);

namespace Tests;

use App\Infraestructure\Database\Model\User;
use App\Infraestructure\Database\Model\Transaction;
use App\Domain\Repository\TransactionRepositoryInterface;
use App\Domain\Services\Notification\NotificationServiceInterface;
use App\Domain\Services\Transaction\TransactionService;
use App\Domain\Services\User\UserServiceInterface;
use App\Domain\Services\Validation\TransactionValidationServiceInterface;
use Hyperf\Testing\TestCase;

class TransactionServiceTest extends TestCase
{
    public function testPerformTransaction()
    {
        $transactionRepo = $this->createMock(TransactionRepositoryInterface::class);
        $userService = $this->createMock(UserServiceInterface::class);
        $validationService = $this->createMock(TransactionValidationServiceInterface::class);
        $notificationService = $this->createMock(NotificationServiceInterface::class);

        $transactionRepo->method('createTransaction')->willReturn(
            new Transaction([
                "id" => "8181b4dc-e5c9-4221-b20c-4decb9e54440",
                "value" => 50.00,
                "payer_id" => 1,
                "payee_id" => 4,
                "created_at" => "2024-03-06 23:27:53",
                "updated_at" => "2024-03-06 23:27:53"
            ])
        );

        $userService->method('getUserById')->willReturn(
            new User(['id' => 'payer_id', 'balance' => 100]),
            new User(['id' => 'payee_id', 'balance' => 50])
        );

        $userService->method('updateBalance')->willReturn(true);

        $notificationService->method('sendNotification')->willReturn(true);

        $transactionService = new TransactionService(
            $transactionRepo,
            $userService,
            $validationService,
            $notificationService
        );

        $transactionData = [
            'payer' => 1,
            'payee' => 4,
            'value' => 50
        ];

        $result = $transactionService->performTransaction($transactionData);

        $this->assertTrue($result);
    }
}
