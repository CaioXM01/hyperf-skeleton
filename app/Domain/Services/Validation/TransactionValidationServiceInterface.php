<?php

namespace App\Domain\Services\Validation;

use App\Domain\DTO\User\UserDto;

interface TransactionValidationServiceInterface
{
    /**
     * Validate the user balance for a transaction.
     *
     * @param UserDto $payer
     * @param float $amount
     * @throws \Exception if the user does not have sufficient balance
     */
    public function validateUserBalance(UserDto $payer, float $amount): void;

    /**
     * Validate the payee type for a transaction.
     *
     * @param UserDto $payer
     * @throws \Exception if the payer is not of the common
     */
    public function validatePayerType(UserDto $payer): void;

     /**
     * Validate users.
     *
     * @param UserDto $payer
     * @throws \Exception if the users not exists
     */
    public function validateUsers(UserDto $payer, UserDto $payee): void;

    /**
     * Handle all validates.
     *
     * @param UserDto $payer
     * @param UserDto $payee
     * @param float $amount
     */
    public function validate(UserDto $payer, UserDto $payee, float $amount): void;
}
