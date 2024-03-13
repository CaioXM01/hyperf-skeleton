<?php

namespace App\Domain\Services\Transaction;

use App\Domain\DTO\Transaction\CreateTransactionDto;
use App\Domain\DTO\User\UserDto;
use App\Domain\Services\User\UserServiceInterface;
use App\Domain\Repository\TransactionRepositoryInterface;
use App\Domain\Services\Notification\NotificationServiceInterface;
use App\Domain\Services\Validation\TransactionValidationServiceInterface;
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

    public function performTransaction(CreateTransactionDto $transactionData): bool
    {
        $payerId = $transactionData->payer;
        $payeeId = $transactionData->payee;
        $amount = $transactionData->value;

        if ($payerId === $payeeId) {
            throw new Exception('Payer and payee cannot be the same user.', StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
        }

        $parallel = new Parallel();
        $parallel->add(fn () => $this->userService->getUserById($payerId));
        $parallel->add(fn () => $this->userService->getUserById($payeeId));
        [$payer, $payee] = $parallel->wait();

        $this->validationService->validate($payer, $payee, $amount);

        $transaction = $this->transactionRepo->createTransaction($transactionData);

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
        UserDto $oldPayer,
        float $oldPayerBalance,
        UserDto $oldPayee,
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

    public function chargebackTransaction(string $transactionId, ?string $chargeback_reason): bool
    {
        $transaction = $this->transactionRepo->findById($transactionId);
        if (!$transaction) {
            throw new Exception('Transaction not found.', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($transaction->chargeback_at) {
            throw new Exception('The transaction has already been chargeback.', StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
        }

        $parallel1 = new Parallel();
        $parallel1->add(fn () => $this->userService->getUserById($transaction->payer_id));
        $parallel1->add(fn () => $this->userService->getUserById($transaction->payee_id));
        [$payer, $payee] = $parallel1->wait();

        $parallel2 = new Parallel();
        $parallel2->add(fn () => $this->userService->updateBalance($payer, $transaction->value, 'credit'));
        $parallel2->add(fn () => $this->userService->updateBalance($payee, $transaction->value, 'debit'));
        $parallel2->add(fn () => $this->transactionRepo->setRefund($transaction->id, $chargeback_reason));
        $parallel2->wait();
        return true;
    }

    public function findAllTransactions(): array
    {
        return $this->transactionRepo->findAll();
    }
}
