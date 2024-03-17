<?php

namespace App\Domain\Services\User;

use App\Domain\DTO\User\CreateUserDto;
use App\Domain\DTO\User\UserDto;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Services\User\UserServiceInterface;
use App\Domain\Entities\Enum\OperationEnum;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Hyperf\Di\Annotation\Inject;

class UserService implements UserServiceInterface
{
    /**
     * @Inject
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var OperationEnum
     */
    private $operationEnum;


    public function __construct(
        UserRepositoryInterface $userRepository,
        OperationEnum $operationEnum
    )
    {
        $this->userRepository = $userRepository;
        $this->operationEnum = $operationEnum;
    }

    public function registerUser(CreateUserDto $userData): bool
    {
        $existingUser = $this->userRepository->findByEmail($userData->email);
        if ($existingUser !== null) {
            throw new Exception("E-mail já cadastrado.");
        }

        $existingUser = $this->userRepository->findByDocument($userData->document);
        if ($existingUser !== null) {
            throw new Exception("CPF/CNPJ já cadastrado.");
        }

        $user = $this->userRepository->createUser($userData);

        if (!$user) {
            throw new Exception("Falha ao cadastrar o usuário.");
        }

        return true;
    }

    public function getUserById(string $userId): ?UserDto
    {
        return $this->userRepository->findById($userId);
    }

    public function getAllUsers(): ?array
    {
        return $this->userRepository->findAll();
    }

    public function updateBalance(UserDto $user, float $amount, string $operation): bool
    {
        if (!$this->operationEnum->isValid($operation)) {
            throw new Exception('Invalid operation.', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if ($operation === OperationEnum::CREDIT) {
            return $this->userRepository->incrementBalance($user, $amount);
        }

        if ($operation === OperationEnum::DEBIT) {
            return $this->userRepository->decrementBalance($user, $amount);
        };
    }
}
