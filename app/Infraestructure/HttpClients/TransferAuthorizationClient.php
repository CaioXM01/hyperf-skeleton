<?php

namespace App\Infraestructure\HttpClients;

use App\Domain\HttpClients\TransferAuthorizationClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class TransferAuthorizationClient implements TransferAuthorizationClientInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param string $baseUrl
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => getenv('AUTHORIZATION_API_BASE_URL'),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Verifique a autorização para a transferência.
     *
     * @return array
     * @throws \Exception
     */
    public function checkAuthorization(): array
    {
        try {
            $response = $this->client->get('5794d450-d2e2-4412-8131-73d0293ac1cc');
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 500;
            $responseData = $response ? json_decode($response->getBody()->getContents(), true) : null;

            $message = $responseData['message'] ?? 'Erro ao verificar a autorização da transferência';

            throw new Exception($message, $statusCode);
        }
    }
}
