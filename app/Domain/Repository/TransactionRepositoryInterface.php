<?php

namespace App\Domain\Repository;

use App\Domain\DTO\Transaction\CreateTransactionDto;
use App\Domain\DTO\Transaction\TransactionDto;

interface TransactionRepositoryInterface
{
    /**
     * Create a new transaction.
     *
     * @param CreateTransactionDto $transactionData
     * @return TransactionDto
     */
    public function createTransaction(CreateTransactionDto $transactionData): TransactionDto;

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
     * @return TransactionDto|null
     */
    public function findById(string $transactionId): ?TransactionDto;

    /**
     * Find all transactions.
     *
     * @return TransactionDto[]|null
     */
    public function findAll(): ?array;

    /**
     * Set a refund for the transaction.
     *
     * @param string $transactionId
     * @param string $chargebackReason
     * @return bool
     */
    public function setRefund(string $transactionId, string $chargebackReason): bool;

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
