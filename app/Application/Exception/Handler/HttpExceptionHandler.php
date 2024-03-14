<?php

declare(strict_types=1);

namespace App\Application\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(
                $throwable->getCode() > 599 ? 500 : $throwable->getCode()
            )
            ->withBody(
                new SwooleStream(
                    json_encode(
                        [
                            'status' => 'error',
                            'message' => $throwable->getMessage()
                        ],
                        JSON_UNESCAPED_UNICODE
                    )
                )
            );
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
