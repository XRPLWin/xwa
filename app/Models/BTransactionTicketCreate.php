<?php

namespace App\Models;

/**
 * Transaction model of type TicketCreate.
 * PK: rAcct-24 SK: <INT> (Ledger index)
 */
class BTransactionTicketCreate extends BTransaction
{
  const TYPE = 24;

  public function toFinalArray(): array
  {
    $array = ['type' => $this::TYPE];
    $array = \array_merge(parent::toFinalArray(),$array);
    
    return $array;
  }

}