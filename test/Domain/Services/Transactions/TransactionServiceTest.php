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
        // Criar mocks para as dependências
        $transactionRepo = $this->createMock(TransactionRepositoryInterface::class);
        $userService = $this->createMock(UserServiceInterface::class);
        $validationService = $this->createMock(TransactionValidationServiceInterface::class);
        $notificationService = $this->createMock(NotificationServiceInterface::class);

        // Configurar stubs e expectativas de métodos mock
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
            new User(['id' => 'payer_id', 'balance' => 100]), // Simulação do pagador
            new User(['id' => 'payee_id', 'balance' => 50])   // Simulação do recebedor
        );

        $userService->method('updateBalance')->willReturn(true); // Simula o sucesso da atualização de saldo

        $notificationService->method('sendNotification')->willReturn(true); // Simula o sucesso do envio de notificação

        // Injete as dependências mockadas na classe TransactionService
        $transactionService = new TransactionService(
            $transactionRepo,
            $userService,
            $validationService,
            $notificationService
        );

        // Dados de transação
        $transactionData = [
            'payer' => 1,
            'payee' => 4,
            'value' => 50
        ];

        // Chame o método a ser testado
        $result = $transactionService->performTransaction($transactionData);

        // Verifique se o resultado é o esperado
        $this->assertTrue($result);
    }
}
