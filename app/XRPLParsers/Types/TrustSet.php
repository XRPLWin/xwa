<?php declare(strict_types=1);

namespace App\XRPLParsers\Types;

use App\XRPLParsers\XRPLParserBase;

final class TrustSet extends XRPLParserBase
{
  /**
   * Parses TrustSet type fields and maps them to $this->data
   * @see https://xrpl.org/transaction-types.html
   * @return void
   */
  protected function parseTypeFields(): void
  {
    # StateCreated - true if trustline created, false if deleted
    $this->data['StateCreated'] = !($this->tx->LimitAmount->value == 0);

    $this->data['Currency'] = $this->tx->LimitAmount->currency;
    $this->data['Amount'] = $this->tx->LimitAmount->value;
    $this->data['Issuer'] = $this->tx->LimitAmount->issuer;
  }

  /**
   * Returns standardized array of relevant data for storing to Dynamo database.
   * key => value one dimensional array which correlates to column => value in DyDb.
   * @return array
   */
  public function toDArray(): array
  {
    $r = [
      't' => $this->data['Date'],
      //'fe' => $this->data['Fee'],
      //'in' => $this->data['In'],
      's' => $this->data['StateCreated'],
      'a' => $this->data['Amount'],
      'c' => $this->data['Currency'],
      'r' => $this->data['Issuer'], //counterparty =
      'i' => $this->data['Issuer'], //= issuer
      'h' => $this->data['hash'],
    ];

    if(\array_key_exists('Fee', $this->data))
      $r['fe'] = $this->data['Fee'];

    if($this->data['In'] === true) //to save space we only store true value
      $r['in'] = true;

    return $r;
  }
}