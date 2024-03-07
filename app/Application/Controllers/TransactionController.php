<?php

namespace App\Application\Controllers;

use App\Domain\Services\Transaction\TransactionServiceInterface;
use App\Infraestructure\HttpClients\TransferAuthorizationClient;
use Fig\Http\Message\StatusCodeInterface;
use Hyperf\Di\Annotation\Inject;

class TransactionController extends AbstractController
{
	/**
	 * @Inject
	 * @var TransactionServiceInterface
	 */
	protected $transactionService;

	public function __construct(
        TransactionServiceInterface $transactionService,
        private TransferAuthorizationClient $client
    )
	{
		$this->transactionService = $transactionService;
	}

	public function performTransaction()
	{
		try {
			$this->transactionService->performTransaction($this->request->all());
			return $this->response->json(['status' => 'ok'], StatusCodeInterface::STATUS_CREATED);
		} catch (\Exception $e) {
			return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

    public function refundTransaction()
	{
        $transactionId = $this->request->route('id');
        $dataRequest = $this->request->all();

		try {
			$this->transactionService->refundTransaction($transactionId, $dataRequest["refound_reason"]);
			return $this->response->json(['status' => 'ok'], StatusCodeInterface::STATUS_CREATED);
		} catch (\Exception $e) {
			return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

    public function findAllTransactions()
	{
		try {
			$transactions = $this->transactionService->findAllTransactions();
			return $this->response->json($transactions, StatusCodeInterface::STATUS_OK);
		} catch (\Exception $e) {
			return $this->response->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() || StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
		}
	}
}
