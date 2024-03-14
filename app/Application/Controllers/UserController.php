<?php

namespace App\Application\Controllers;

use App\Application\Services\Validation\Request\UserRequest;
use App\Application\Resources\ResponseResource;
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

    /**
     * @Inject
     * @var ResponseResource
     */
    protected $responseResource;

    public function __construct(
        UserServiceInterface $userService,
        ResponseResource $responseResource
    ) {
        $this->userService = $userService;
        $this->responseResource = $responseResource;
    }

    public function register(UserRequest $request)
    {
        $request->validated();

        $createUserDto = new CreateUserDto(
            $request->input('name'),
            $request->input('email'),
            $request->input('document'),
            $request->input('password'),
            $request->input('balance'),
            $request->input('type')
        );

        $this->userService->registerUser($createUserDto);
        return $this->response->json($this->responseResource->toArray(), StatusCodeInterface::STATUS_CREATED);
    }

    public function getUserById()
    {
        $userId = $this->request->route('id');
        $userResponse = $this->userService->getUserById($userId);
        return $this->response->json($this->responseResource->toArray($userResponse), StatusCodeInterface::STATUS_OK);
    }

    public function getAllUsers()
    {
        $users = $this->userService->getAllUsers();
        return $this->response->json($this->responseResource->toArray($users), StatusCodeInterface::STATUS_OK);
    }
}
