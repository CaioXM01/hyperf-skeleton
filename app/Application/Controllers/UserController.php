<?php

namespace App\Application\Controllers;

use App\Application\Services\Validation\Request\UserRequest;
use App\Domain\DTO\User\CreateUserDto;
use App\Domain\Services\User\UserServiceInterface;
use Fig\Http\Message\StatusCodeInterface;
use Hyperf\Di\Annotation\Inject;

class UserController extends AbstractController
{
    /**
     * @Inject
     * @var UserServiceInterface
     */
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request)
    {
        $request->validated();
        try {
            $createUserDto = new CreateUserDto(
                $request->input('name'),
                $request->input('email'),
                $request->input('document'),
                $request->input('password'),
                $request->input('balance'),
                $request->input('type')
            );

            $this->userService->registerUser($createUserDto);
            return $this->response->json(['status' => 'ok'], StatusCodeInterface::STATUS_CREATED);
        } catch (\Exception $e) {
            return $this->response->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->withStatus($e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUserById()
    {
        $userId = $this->request->route('id');

        $userResponse = $this->userService->getUserById($userId);
        if ($userResponse === null) {
            return $this->response->json(['error' => 'User not found'], StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return $this->response->json($userResponse, StatusCodeInterface::STATUS_OK);
    }

    public function getAllUsers()
    {
        $users = $this->userService->getAllUsers();
        return $this->response->json($users, StatusCodeInterface::STATUS_OK);
    }
}
