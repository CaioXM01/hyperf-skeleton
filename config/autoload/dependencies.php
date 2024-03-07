<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Domain\HttpClients\NotificationClientInterface;
use App\Domain\HttpClients\TransferAuthorizationClientInterface;
use App\Domain\Repository\TransactionRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Services\User\UserService;
use App\Domain\Services\User\UserServiceInterface;
use App\Domain\Services\Transaction\TransactionService;
use App\Domain\Services\Transaction\TransactionServiceInterface;
use App\Domain\Services\Notification\NotificationService;
use App\Domain\Services\Notification\NotificationServiceInterface;
use App\Domain\Services\Validation\TransactionValidationService;
use App\Domain\Services\Validation\TransactionValidationServiceInterface;
use App\Infraestructure\Database\Repository\TransactionRepository;
use App\Infraestructure\Database\Repository\UserRepository;
use App\Infraestructure\HttpClients\NotificationClient;
use App\Infraestructure\HttpClients\TransferAuthorizationClient;

return [
  UserServiceInterface::class => UserService::class,
  UserRepositoryInterface::class => UserRepository::class,
  TransactionRepositoryInterface::class => TransactionRepository::class,
  TransactionServiceInterface::class => TransactionService::class,
  TransactionValidationServiceInterface::class => TransactionValidationService::class,
  TransferAuthorizationClientInterface::class => TransferAuthorizationClient::class,
  NotificationClientInterface::class => NotificationClient::class,
  NotificationServiceInterface::class => NotificationService::class
];
