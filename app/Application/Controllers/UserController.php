<?php

namespace App\Application\Controllers;

use App\Domain\Services\User\UserServiceInterface;
use App\Infraestructure\Database\Model\User;
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

    public function register()
    {
        try {
            $this->userService->registerUser(New User($this->request->all()));
            return $this->response->json(['status' => 'ok'], StatusCodeInterface::STATUS_CREATED);
        } catch (\Exception $e) {
            return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
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
