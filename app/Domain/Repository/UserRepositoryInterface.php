<?php

namespace App\Domain\Repository;

use App\Domain\DTO\User\CreateUserDto;
use App\Domain\DTO\User\UserDto;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param CreateUserDto $userData
     * @return bool
     */
    public function createUser(CreateUserDto $userData): bool;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return UserDto|null
     */
    public function findByEmail(string $email): ?UserDto;

    /**
     * Find a user by userId.
     *
     * @param string $userId
     * @return UserDto|null
     */
    public function findById(string $userId): ?UserDto;

    /**
     * Find all users.
     *
     * @return UserDto[]|null
     */
    public function findAll(): ?array;

    /**
     * Find a user by document.
     *
     * @param string $document
     * @return UserDto|null
     */
    public function findByDocument(string $document): ?UserDto;

    /**
     * Check if the user has sufficient balance for a transaction.
     *
     * @param UserDto $user
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(UserDto $user, float $amount): bool;

    /**
     * Method to increase the user's balance.
     *
     * @param UserDto $user
     * @param float $amount
     * @return bool
     */
    public function incrementBalance(UserDto $user, float $amount): bool;

    /**
     * Method to decrement the user's balance.
     *
     * @param UserDto $user
     * @param float $amount
     * @return bool
     */
    public function decrementBalance(UserDto $user, float $amount): bool;
}
