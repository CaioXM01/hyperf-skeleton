<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\DTO\Transaction\CreateTransactionDto;
use App\Domain\DTO\Transaction\TransactionDto;
use App\Domain\DTO\User\UserDto;
use App\Domain\Repository\TransactionRepositoryInterface;
use App\Domain\Services\Notification\NotificationServiceInterface;
use App\Domain\Services\Transaction\TransactionService;
use App\Domain\Services\User\UserServiceInterface;
use App\Domain\Services\Validation\TransactionValidationServiceInterface;
use Hyperf\Testing\TestCase;
use Carbon\Carbon;

class TransactionServiceTest extends TestCase
{
    public function testPerformTransaction()
    {
        $transactionRepo = $this->createMock(TransactionRepositoryInterface::class);
        $userService = $this->createMock(UserServiceInterface::class);
        $validationService = $this->createMock(TransactionValidationServiceInterface::class);
        $notificationService = $this->createMock(NotificationServiceInterface::class);

        $transactionRepo->method('createTransaction')->willReturn(
            new TransactionDto(
                "8181b4dc-e5c9-4221-b20c-4decb9e54440",
                50.00,
                1,
                4,
                null,
                null,
                null,
                null,
                null,
                null
            )
        );

        $userService->method('getUserById')->willReturn(
            new UserDto(
                1,
                'Test payer',
                'payer@example.com',
                '111111111111111',
                1500,
                'common',
                Carbon::now(),
                Carbon::now()
            ),
            new UserDto(
                2,
                'Test payee',
                'payee@example.com',
                '111111111111111',
                2000,
                'common',
                Carbon::now(),
                Carbon::now()
            )
        );

        $userService->method('updateBalance')->willReturn(true);

        $notificationService->method('sendNotification')->willReturn(true);

        $transactionService = new TransactionService(
            $transactionRepo,
            $userService,
            $validationService,
            $notificationService
        );

        $createTransactionDto = new CreateTransactionDto(
            50,
            1,
            2,
        );

        $result = $transactionService->performTransaction($createTransactionDto);

        $this->assertTrue($result);
    }
}
