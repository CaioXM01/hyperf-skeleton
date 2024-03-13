<?php

namespace App\Domain\Services\User;

use App\Domain\DTO\User\CreateUserDto;
use App\Domain\DTO\User\UserDto;

interface UserServiceInterface
{
    /**
     * Register a new user.
     *
     * @param CreateUserDto $userData
     * @return bool
     * @throws \Exception if registration fails
     */
    public function registerUser(CreateUserDto $userData): bool;

    /**
     * Get a user by userId.
     *
     * @param string $userId
     * @return UserDto|null
     */
    public function getUserById(string $userId): ?UserDto;

    /**
     * Get all users.
     *
     * @return UserDto[]|null
     */
    public function getAllUsers(): ?array;

    /**
     * Update user balance.
     *
     * @param UserDto $user
     * @param float $amount
     * @param string $operation "debit"|"credit"
     * @return bool
     */
    public function updateBalance(UserDto $user, float $amount, string $operation): bool;

    /**
     * Rollback user balance.
     *
     * @param UserDto $user
     * @param float $userBalance
     * @return bool
     */
    public function rollbackBalance(UserDto $user, float $userBalance): bool;
}
