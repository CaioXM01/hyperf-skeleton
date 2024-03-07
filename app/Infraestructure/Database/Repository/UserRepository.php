<?php

namespace App\Infraestructure\Database\Repository;

use App\Infraestructure\Database\Model\User;
use App\Domain\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $userModel;

    /**
     * @param User $userModel
     */
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Method to create a new user.
     *
     * @param array $userData
     * @return User
     */
    public function createUser(User $userData): bool
    {
        $user = new User([
            "name" => $userData['name'],
            "email" => $userData['email'],
            "password" => password_hash($userData['password'], PASSWORD_BCRYPT),
            "document" => $userData['document'],
            "balance" => $userData['balance'],
            "type" => $userData['type']
        ]);

        return $user->save();
    }

    /**
     * Method to retrieve all users.
     *
     * @return User[]|null
     */
    public function findAll(): ?array
    {
        return $this->userModel->get()->toArray();
    }


    /**
     * Method to retrieve a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->userModel->where('email', $email)->first();
    }

    /**
     * Method to retrieve a user by userId.
     *
     * @param string $userId
     * @return User|null
     */
    public function findById(string $userId): ?User
    {
        return $this->userModel->find($userId);
    }

    /**
     * Method to retrieve a user by document.
     *
     * @param string $document
     * @return User|null
     */
    public function findByDocument(string $document): ?User
    {
        return $this->userModel->where('document', $document)->first();
    }

    /**
     * Method to check if the user has sufficient balance for a transaction.
     *
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(User $user, float $amount): bool
    {
        return $user->balance >= $amount;
    }

    /**
     * Method to update the user's balance after a transaction.
     *
     * @param User $user
     * @param float $newUserBalance
     * @param string $operation
     * @return bool
     */
    public function updateBalance(User $user, float $newUserBalance): bool
    {
        $user->balance = $newUserBalance;
        return $user->save();
    }
}
