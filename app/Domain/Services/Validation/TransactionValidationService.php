<?php

namespace App\Domain\Services\Validation;

use App\Domain\HttpClients\TransferAuthorizationClientInterface;
use App\Infraestructure\Database\Model\User;
use Hyperf\Coroutine\Parallel;
use Fig\Http\Message\StatusCodeInterface;
use Exception;

class TransactionValidationService implements TransactionValidationServiceInterface
{
    /**
     * @Inject
     * @var TransferAuthorizationClientInterface
     */
    protected $authorizationClient;

    public function __construct(
        TransferAuthorizationClientInterface $authorizationClient
    ) {
        $this->authorizationClient = $authorizationClient;
    }

    public function checkExternalAuthorizerService(): void
    {
        $authorization = $this->authorizationClient->checkAuthorization();
        if ($authorization['message'] !== 'Autorizado') {
            throw new Exception('The external authorizing service did not authorize the transfer.', StatusCodeInterface::STATUS_FORBIDDEN);
        }
    }

    public function validateUsers(?User $payer, ?User $payee): void
    {
        if (!$payer) {
            throw new Exception('User payer is not found.', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if (!$payee) {
            throw new Exception('User payee is not found.', StatusCodeInterface::STATUS_NOT_FOUND);
        }
    }

    public function validateUserBalance(?User $payer, float $amount): void
    {
        if (!$payer || $payer->balance < $amount) {
            throw new Exception('User does not have sufficient balance.', StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
        }
    }

    public function validatePayerType(?User $payer): void
    {
        if (!$payer || $payer->type === 'shopkeeper') {
            throw new Exception('Shopkeeper cannot make transfers.', StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED);
        }
    }

    public function validateAmountValue(float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('The transfer amount must be greater than zero.', StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
        }
    }

    public function validate(?User $payer, ?User $payee, float $amount): void
    {
        $parallel = new Parallel();

        $errors = [];

        $parallel->add(function () use ($payer, $payee, &$errors) {
            try {
                $this->validateUsers($payer, $payee);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        });

        $parallel->add(function () use ($amount, &$errors) {
            try {
                $this->validateAmountValue($amount);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        });

        $parallel->add(function () use ($payer, $amount, &$errors) {
            try {
                $this->validateUserBalance($payer, $amount);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        });

        $parallel->add(function () use ($payer, &$errors) {
            try {
                $this->validatePayerType($payer);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        });

        $parallel->add(function () use (&$errors) {
            try {
                $this->checkExternalAuthorizerService();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        });

        $parallel->wait();

        if (!empty($errors)) {
            throw new Exception(implode(' && ', $errors), StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY);
        }
    }
}
