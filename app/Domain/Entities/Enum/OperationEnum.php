<?php

namespace App\Domain\Entities\Enum;

class OperationEnum
{
  public const CREDIT = 'credit';
  public const DEBIT = 'debit';

  /**
   * Verifica se uma operação é válida.
   *
   * @param string $operation
   * @return bool
   */
  public static function isValid(string $operation): bool
  {
    return in_array($operation, [self::CREDIT, self::DEBIT]);
  }
}
