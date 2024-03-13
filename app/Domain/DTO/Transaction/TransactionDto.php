<?php

namespace App\Domain\DTO\Transaction;

use Carbon\Carbon;

class TransactionDto
{
    public string $id;
    public float $value;
    public int $payer_id;
    public int $payee_id;
    public ?string $chargeback_reason;
    public ?Carbon $notified_at;
    public ?Carbon $transferred_at;
    public ?Carbon $chargeback_at;
    public ?Carbon $created_at;
    public ?Carbon $updated_at;

    public function __construct(
        string $id,
        float $value,
        int $payer_id,
        int $payee_id,
        ?string $chargeback_reason,
        ?Carbon $notified_at,
        ?Carbon $transferred_at,
        ?Carbon $chargeback_at,
        ?Carbon $created_at,
        ?Carbon $updated_at
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->payer_id = $payer_id;
        $this->payee_id = $payee_id;
        $this->chargeback_reason = $chargeback_reason;
        $this->notified_at = $notified_at;
        $this->transferred_at = $transferred_at;
        $this->chargeback_at = $chargeback_at;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
