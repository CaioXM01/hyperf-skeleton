<?php

namespace App\Domain\Repository;

use App\Infraestructure\Database\Model\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $userData
     * @return User
     */
    public function createUser(User $userData): bool;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by userId.
     *
     * @param string $userId
     * @return User|null
     */
    public function findById(string $userId): ?User;

    /**
     * Find all users.
     *
     * @return User[]|null
     */
    public function findAll(): ?array;

    /**
     * Find a user by document.
     *
     * @param string $document
     * @return User|null
     */
    public function findByDocument(string $document): ?User;

    /**
     * Check if the user has sufficient balance for a transaction.
     *
     * @param User $user
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(User $user, float $amount): bool;

    /**
     * Update the user's balance after a transaction.
     *
     * @param User $user
     * @param float $newUserBalance
     * @return bool
     */
    public function updateBalance(User $user, float $newUserBalance): bool;
}
