<?php

namespace App\Domain\Services\Validation;

use App\Infraestructure\Database\Model\User;

interface TransactionValidationServiceInterface
{
    /**
     * Validate the user balance for a transaction.
     *
     * @param User $payer
     * @param float $amount
     * @throws \Exception if the user does not have sufficient balance
     */
    public function validateUserBalance(User $payer, float $amount): void;

    /**
     * Validate the payee type for a transaction.
     *
     * @param User $payer
     * @throws \Exception if the payer is not of the common
     */
    public function validatePayerType(User $payer): void;

     /**
     * Validate users.
     *
     * @param User $payer
     * @throws \Exception if the users not exists
     */
    public function validateUsers(User $payer, User $payee): void;

    /**
     * Handle all validates.
     *
     * @param User $payer
     * @param User $payee
     * @param float $amount
     */
    public function validate(User $payer, User $payee, float $amount): void;
}
