<?php

namespace App\Domain\Services\Transaction;

interface TransactionServiceInterface
{
    /**
     * Perform a transaction between two users.
     *
     * @param array $transactionData
     * @return bool
     * @throws \Exception if the transaction fails
     */
    public function performTransaction(array $transactionData): bool;

    /**
     * Refund a transaction.
     *
     * @param string $transactionId
     * @return bool
     * @throws \Exception if the refund fails
     */
    public function refundTransaction(string $transactionId): bool;

    /**
     * Find transactions by user id.
     *
     * @param string $userId
     * @return array
     * @throws \Exception if the find fails
     */
    public function findTransactionsByUser(string $userId): array;
}