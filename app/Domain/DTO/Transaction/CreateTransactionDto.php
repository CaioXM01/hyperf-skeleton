<?php

namespace App\Domain\DTO\Transaction;

class CreateTransactionDto
{
    public float $value;
    public int $payer;
    public int $payee;

    public function __construct(float $value, int $payer, int $payee)
    {
        $this->value = $value;
        $this->payer = $payer;
        $this->payee = $payee;
    }
}
