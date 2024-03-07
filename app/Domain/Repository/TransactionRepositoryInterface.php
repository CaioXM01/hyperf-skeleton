<?php

namespace App\Domain\Repository;

use App\Infraestructure\Database\Model\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * Create a new transaction.
     *
     * @param array $transactionData
     * @return Transaction
     */
    public function createTransaction(array $transactionData): Transaction;

    /**
     * Rollback a transaction by its ID.
     *
     * @param string $transactionId
     * @return bool
     */
    public function rollbackTransactionById(string $transactionId): bool;

    /**
     * Find a transaction by ID.
     *
     * @param string $transactionId
     * @return Transaction|null
     */
    public function findById(string $transactionId): ?Transaction;

    /**
     * Find all transactions.
     *
     * @return Transaction[]|null
     */
    public function findAll(): ?array;

    /**
     * Set a refund for the transaction.
     *
     * @param string $transactionId
     * @param string $refoundReason
     * @return bool
     */
    public function setRefund(string $transactionId, string $refoundReason): bool;

    /**
     * Set a transferred_at for the transaction.
     *
     * @param string $transactionId
     * @return bool
     */
    public function setTransferred(string $transactionId): bool;

    /**
     * Set a notified_at for the transaction.
     *
     * @param string $transactionId
     * @return bool
     */
    public function setNotification(string $transactionId): bool;
}
