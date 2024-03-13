<?php

namespace App\Infraestructure\Database\Repository;

use App\Domain\DTO\User\CreateUserDto;
use App\Domain\DTO\User\UserDto;
use App\Infraestructure\Database\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use Carbon\Carbon;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var Carbon
     */
    protected $carbon;

    /**
     * @param User $userModel
     */
    public function __construct(
        User $userModel,
        Carbon $carbon
    ) {
        $this->userModel = $userModel;
        $this->carbon = $carbon;
    }

    /**
     * Method to create a new user.
     *
     * @param CreateUserDto $userData
     * @return bool
     */
    public function createUser(CreateUserDto $userData): bool
    {
        $user = new User([
            "name" => $userData->name,
            "email" => $userData->email,
            "password" => password_hash($userData->password, PASSWORD_BCRYPT),
            "document" => $userData->document,
            "balance" => $userData->balance,
            "type" => $userData->type
        ]);

        return $user->save();
    }

    /**
     * Method to retrieve all users.
     *
     * @return UserDto[]|null
     */
    public function findAll(): ?array
    {
        return $this->userModel->get()->toArray();
    }


    /**
     * Method to retrieve a user by email.
     *
     * @param string $email
     * @return UserDto|null
     */
    public function findByEmail(string $email): ?UserDto
    {
        $user = $this->userModel->where('email', $email)->first();
        return $this->mapSingleToDto($user);
    }

    /**
     * Method to retrieve a user by userId.
     *
     * @param string $userId
     * @return UserDto|null
     */
    public function findById(string $userId): ?UserDto
    {
        $user = $this->userModel->find($userId)->toArray();
        return $this->mapSingleToDto($user);
    }

    /**
     * Method to retrieve a user by document.
     *
     * @param string $document
     * @return UserDto|null
     */
    public function findByDocument(string $document): ?UserDto
    {
        $user = $this->userModel->where('document', $document)->first();
        return $this->mapSingleToDto($user);
    }

    /**
     * Method to check if the user has sufficient balance for a transaction.
     *
     * @param UserDto $user
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance(UserDto $user, float $amount): bool
    {
        return $user->balance >= $amount;
    }

    /**
     * Method to update the user's balance after a transaction.
     *
     * @param UserDto $user
     * @param float $newUserBalance
     * @return bool
     */
    public function updateBalance(UserDto $user, float $newUserBalance): bool
    {
        $userToSave = $this->userModel->find($user->id);

        $userToSave->balance = $newUserBalance;

        return $userToSave->save();
    }

    private function mapSingleToDto(?array $user): ?UserDto
    {
        if(!$user) {
            return null;
        }

        return new UserDto(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['document'],
            $user['balance'],
            $user['type'],
            $this->carbon->parse($user['created_at']),
            $this->carbon->parse($user['updated_at'])
        );
    }
}
