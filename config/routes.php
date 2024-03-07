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

use App\Application\Controllers\TransactionController;
use Hyperf\HttpServer\Router\Router;
use App\Application\Controllers\UserController;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Application\Controllers\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::addGroup('/users', function () {
    Router::get('', [UserController::class, 'getAllUsers']);
    Router::get('/{id}', [UserController::class, 'getUserById']);
    Router::post('', [UserController::class, 'register']);
});

Router::addGroup('/transaction', function () {
    Router::post('', [TransactionController::class, 'performTransaction']);
});
