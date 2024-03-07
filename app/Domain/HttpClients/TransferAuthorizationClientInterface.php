<?php

namespace App\Domain\HttpClients;

interface TransferAuthorizationClientInterface
{
    /**
     * Verifica a autorização para a transferência.
     *
     * @return array
     * @throws \Exception
     */
    public function checkAuthorization(): array;
}
