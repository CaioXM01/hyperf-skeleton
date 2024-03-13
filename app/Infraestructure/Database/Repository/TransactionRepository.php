<?php

namespace App\Infraestructure\Database\Repository;

use App\Domain\DTO\Transaction\CreateTransactionDto;
use App\Domain\DTO\Transaction\TransactionDto;
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
     * @param CreateTransactionDto $transactionData
     * @return TransactionDto
     */
    public function createTransaction(CreateTransactionDto $transactionData): TransactionDto
    {
        $transaction = new Transaction([
            "id" => Uuid::uuid4()->toString(),
            "value" => $transactionData->value,
            "payer_id" => $transactionData->payer,
            "payee_id" => $transactionData->payee
        ]);

        $transaction->save();
        return $this->mapSingleToDto($transaction);
    }

    public function rollbackTransactionById(string $transactionId): bool
    {
        $transaction = $this->transactionModel->find($transactionId);
        if ($transaction === null) {
            return false;
        }

        return $transaction->delete();
    }

    /**
     * Find a transaction by ID.
     *
     * @param string $transactionId
     * @return TransactionDto|null
     */
    public function findById(string $transactionId): ?TransactionDto
    {
        $transaction = $this->transactionModel->find($transactionId);
        return $this->mapSingleToDto($transaction);
    }

    /**
     * Find all transactions.
     *
     * @return TransactionDto[]|null
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

    private function mapSingleToDto(?Transaction $transaction): ?TransactionDto
    {
        if(!$transaction) {
            return null;
        }

        return new TransactionDto(
            $transaction['id'],
            $transaction['value'],
            $transaction['payer_id'],
            $transaction['payee_id'],
            $transaction['chargeback_reason'],
            $transaction['notified_at'] ? $this->carbon->parse($transaction['notified_at']) : null,
            $transaction['transferred_at'] ? $this->carbon->parse($transaction['transferred_at']) : null,
            $transaction['chargeback_at'] ? $this->carbon->parse($transaction['chargeback_at']) : null,
            $transaction['created_at'] ? $this->carbon->parse($transaction['created_at']) : null,
            $transaction['updated_at'] ? $this->carbon->parse($transaction['updated_at']) : null
        );
    }
}
