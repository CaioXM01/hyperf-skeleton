<?php

namespace App\Domain\Services\Transaction;

use App\Domain\Services\User\UserServiceInterface;
use App\Domain\Repository\TransactionRepositoryInterface;
use App\Domain\Services\Notification\NotificationServiceInterface;
use App\Domain\Services\Validation\TransactionValidationServiceInterface;
use App\Infraestructure\Database\Model\User;
use Hyperf\Coroutine\Parallel;
use Fig\Http\Message\StatusCodeInterface;
use Exception;

class TransactionService implements TransactionServiceInterface
{
    /**
     * @Inject
     * @var UserServiceInterface
     */
    protected $userService;
    /**
     * @Inject
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepo;
    /**
     * @Inject
     * @var TransactionValidationServiceInterface
     */
    protected $validationService;
    /**
     * @Inject
     * @var NotificationServiceInterface
     */
    protected $notificationService;

    public function __construct(
        TransactionRepositoryInterface $transactionRepo,
        UserServiceInterface $userService,
        TransactionValidationServiceInterface $validationService,
        NotificationServiceInterface $notificationService
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->userService = $userService;
        $this->validationService = $validationService;
        $this->notificationService = $notificationService;
    }

    public function performTransaction(array $transactionData): bool
    {
        // Extrair dados da transação
        $payerId = $transactionData['payer'];
        $payeeId = $transactionData['payee'];
        $amount = $transactionData['value'];

        // Verificar se o pagador e o recebedor são diferentes
        if ($payerId === $payeeId) {
            throw new Exception('Payer and payee cannot be the same user.', StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
        }

        $parallel = new Parallel();
        $parallel->add(fn () => $this->userService->getUserById($payerId));
        $parallel->add(fn () => $this->userService->getUserById($payeeId));
        [$payer, $payee] = $parallel->wait();

        $this->validationService->validate($payer, $payee, $amount);

        $transaction = $this->transactionRepo->createTransaction([
            "value" => $amount,
            "payer_id" => $payerId,
            "payee_id" => $payeeId,
        ]);

        $currentPayerBalance = $payer->balance;
        $currentPayeeBalance = $payee->balance;

        try {
            $parallel = new Parallel();
            $parallel->add(fn () => $this->userService->updateBalance($payer, $amount, 'debit'));
            $parallel->add(fn () => $this->userService->updateBalance($payee, $amount, 'credit'));
            $parallel->add(fn () => $this->transactionRepo->setTransferred($transaction->id));
            $parallel->wait();
        } catch (Exception $e) {
            $this->rollbackTransaction($payer, $currentPayerBalance, $payee, $currentPayeeBalance, $transaction->id);
            throw new Exception('Transaction failed: ' . $e->getMessage(), $e->getCode());
        }

        $this->sendNotification($transaction->id);
        return true;
    }

    public function rollbackTransaction(
        User $oldPayer,
        float $oldPayerBalance,
        User $oldPayee,
        float $oldPayeeBalance,
        string $transactionId
    ): bool
    {
        $parallel = new Parallel();
        $parallel->add(fn () => $this->userService->rollbackBalance($oldPayer, $oldPayerBalance));
        $parallel->add(fn () => $this->userService->rollbackBalance($oldPayee, $oldPayeeBalance));
        $parallel->add(fn () => $this->transactionRepo->rollbackTransactionById($transactionId));
        $parallel->wait();

        return true;
    }

    public function sendNotification(string $transactionId): bool
    {
        $notification = $this->notificationService->sendNotification();
        if ($notification) {
            $this->transactionRepo->setNotification($transactionId);
            return true;
        }
        return false;
    }

    public function refundTransaction(string $transactionId): bool
    {
        // Implementar lógica de reembolso da transação
        // ...
        return true;
    }

    public function findTransactionsByUser(string $userId): array
    {
        // Implementar lógica para buscar transações por usuário
        // ...
        return [];
    }
}
