<?php

declare(strict_types=1);

namespace App\Application\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();
        /** @var ValidationException $throwable */
        $body = $throwable->validator->errors()->all();
        return $response->withHeader('content-type', 'application/json')
            ->withStatus($throwable->status)
            ->withBody(new SwooleStream(json_encode([
                'status' => "error",
                'message' => 'Erros de validação',
                'errors' => $body,
            ])));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
