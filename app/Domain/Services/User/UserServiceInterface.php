<?php

namespace App\Domain\Services\User;

use App\Infraestructure\Database\Model\User;

interface UserServiceInterface
{
    /**
     * Register a new user.
     *
     * @param User $userData
     * @return bool
     * @throws \Exception if registration fails
     */
    public function registerUser(User $userData): bool;

    /**
     * Get a user by userId.
     *
     * @param string $userId
     * @return User|null
     */
    public function getUserById(string $userId): ?User;

    /**
     * Get all users.
     *
     * @return User[]|null
     */
    public function getAllUsers(): ?array;

    /**
     * Update user balance.
     *
     * @param User $user
     * @param float $amount
     * @param string $operation "debit"|"credit"
     * @return bool
     */
    public function updateBalance(User $user, float $amount, string $operation): bool;

    /**
     * Rollback user balance.
     *
     * @param User $user
     * @param float $userBalance
     * @return bool
     */
    public function rollbackBalance(User $user, float $userBalance): bool;
}
