<?php

namespace App\Application\Controllers;

use App\Application\Services\Validation\Request\TransactionRequest;
use App\Application\Services\Validation\Request\ChargebackTransactionRequest;
use App\Application\Resources\ResponseResource;
use App\Domain\DTO\Transaction\CreateTransactionDto;
use App\Domain\Services\Transaction\TransactionServiceInterface;
use Fig\Http\Message\StatusCodeInterface;
use Hyperf\Di\Annotation\Inject;

class TransactionController extends AbstractController
{
	/**
	 * @Inject
	 * @var TransactionServiceInterface
	 */
	protected $transactionService;

    /**
     * @Inject
     * @var ResponseResource
     */
    protected $responseResource;

	public function __construct(
        TransactionServiceInterface $transactionService,
        ResponseResource $responseResource
    ) {
		$this->transactionService = $transactionService;
        $this->responseResource = $responseResource;
	}

	public function performTransaction(TransactionRequest $request)
	{
        $request->validated();
		try {
            $createTransactionDto = new CreateTransactionDto(
                $request->input('value'),
                $request->input('payer'),
                $request->input('payee'),
            );
			$this->transactionService->performTransaction($createTransactionDto);
            return $this->response->json($this->responseResource->toArray(), StatusCodeInterface::STATUS_CREATED);
		} catch (\Exception $e) {
			return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

    public function chargebackTransaction(ChargebackTransactionRequest $request)
	{
        $transactionId = $this->request->route('id');
        $dataRequest = $request->validated();

		try {
			$this->transactionService->chargebackTransaction($transactionId, $dataRequest["chargeback_reason"]);
            return $this->response->json($this->responseResource->toArray(), StatusCodeInterface::STATUS_CREATED);
		} catch (\Exception $e) {
			return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

    public function findAllTransactions()
	{
		try {
			$transactions = $this->transactionService->findAllTransactions();
            return $this->response->json($this->responseResource->toArray($transactions), StatusCodeInterface::STATUS_OK);
		} catch (\Exception $e) {
			return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
		}
	}
}
