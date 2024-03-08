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
     * @param string|null $chargeback_reason
     * @return bool
     * @throws \Exception if the refund fails
     */
    public function chargebackTransaction(string $transactionId, ?string $chargeback_reason): bool;

    /**
     * Find all transactions.
     *
     * @return array
     * @throws \Exception if the find fails
     */
    public function findAllTransactions(): array;
}
