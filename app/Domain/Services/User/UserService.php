<?php

namespace App\Domain\Services\User;

use App\Infraestructure\Database\Model\User;
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

    public function registerUser(User $userData): bool
    {
        // Verificar se o e-mail já está cadastrado
        $existingUser = $this->userRepository->findByEmail($userData['email']);
        if ($existingUser !== null) {
            throw new Exception("E-mail já cadastrado.");
        }

        $existingUser = $this->userRepository->findByDocument($userData['document']);
        if ($existingUser !== null) {
            throw new Exception("CPF/CNPJ já cadastrado.");
        }

        // Criar o novo usuário
        $user = $this->userRepository->createUser($userData);

        // Verificar se o usuário foi criado com sucesso
        if (!$user) {
            throw new Exception("Falha ao cadastrar o usuário.");
        }

        return true;
    }

    public function getUserById(string $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function getAllUsers(): ?array
    {
        return $this->userRepository->findAll();
    }

    public function updateBalance(User $user, float $amount, string $operation): bool
    {
        if (!$this->operationEnum->isValid($operation)) {
            throw new Exception('Invalid operation.', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $newUserBalance = $user->balance;

        echo  $user->balance,"\n\n";

        if ($operation === OperationEnum::CREDIT) {
            $newUserBalance += $amount;
        }

        if ($operation === OperationEnum::DEBIT) {
            $newUserBalance -= $amount;
        }

        echo $user->id, " ", $operation, " ", $newUserBalance, "\n\n";

        return $this->userRepository->updateBalance($user, $newUserBalance);
    }

    public function rollbackBalance(User $user, float $userBalance): bool
    {
        return $this->userRepository->updateBalance($user, $userBalance);
    }
}
