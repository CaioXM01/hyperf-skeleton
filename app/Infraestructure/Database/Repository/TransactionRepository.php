<?php

namespace App\Infraestructure\Database\Repository;

use App\Domain\Repository\TransactionRepositoryInterface;
use App\Infraestructure\Database\Model\Transaction;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var Carbon
     */
    protected $carbon;

    protected $transactionModel;

    public function __construct(
        Transaction $transactionModel,
        Carbon $carbon
    )
    {
        $this->transactionModel = $transactionModel;
        $this->carbon = $carbon;
    }

    /**
     * Create a new transaction.
     *
     * @param Transaction $transactionData
     * @return bool
     */
    public function createTransaction(array $transactionData): Transaction
    {
        $transaction = new Transaction([
            "id" => Uuid::uuid4()->toString(),
            "value" => $transactionData['value'],
            "payer_id" => $transactionData['payer_id'],
            "payee_id" => $transactionData['payee_id']
        ]);

        $transaction->save();
        return $transaction;
    }

    public function rollbackTransactionById(string $transactionId): bool
    {
        $transaction = $this->findById($transactionId);
        if ($transaction === null) {
            return false;
        }

        return $transaction->delete();
    }

    /**
     * Find a transaction by ID.
     *
     * @param string $transactionId
     * @return Transaction|null
     */
    public function findById(string $transactionId): ?Transaction
    {
        return $this->transactionModel->find($transactionId);
    }

    /**
     * Find all transactions.
     *
     * @return Transaction[]|null
     */
    public function findAll(): ?array
    {
        return $this->transactionModel->all()->toArray();
    }

    /**
     * Set a refund for the transaction.
     *
     * @param string $transactionId
     * @param string $chargebackReason
     * @return bool
     */
    public function setRefund(string $transactionId, string $chargebackReason): bool
    {
        $transaction = $this->transactionModel->find($transactionId);
        if ($transaction) {
            $transaction->chargeback_at = $this->carbon->now();
            $transaction->chargeback_reason = $chargebackReason;
            $transaction->save();
            return true;
        }
        return false;
    }

    /**
     * Set a transferred_at for the transaction.
     *
     * @param string $transactionId
     * @return bool
     */
    public function setTransferred(string $transactionId): bool
    {
        $transaction = $this->transactionModel->find($transactionId);
        if ($transaction) {
            $transaction->transferred_at = $this->carbon->now();
            $transaction->save();
            return true;
        }
        return false;
    }

    /**
     * Set a notified_at for the transaction.
     *
     * @param string $transactionId
     * @return bool
     */
    public function setNotification(string $transactionId): bool
    {
        $transaction = $this->transactionModel->find($transactionId);
        if ($transaction) {
            $transaction->notified_at = $this->carbon->now();
            $transaction->save();
            return true;
        }
        return false;
    }
}
