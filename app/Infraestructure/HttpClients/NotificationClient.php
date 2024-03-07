<?php

namespace App\Infraestructure\HttpClients;

use App\Domain\HttpClients\NotificationClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class NotificationClient implements NotificationClientInterface
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
            'base_uri' => getenv('NOTIFICATION_API_BASE_URL'),
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
    public function sendNotification(): array
    {
        try {
            $response = $this->client->get('54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6');
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 500;
            $responseData = $response ? json_decode($response->getBody()->getContents(), true) : null;

            $message = $responseData['message'] ?? 'Erro ao enviar notificação';

            throw new Exception($message, $statusCode);
        }
    }
}
