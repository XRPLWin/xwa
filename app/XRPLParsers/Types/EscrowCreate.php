<?php declare(strict_types=1);

namespace App\XRPLParsers\Types;

use App\XRPLParsers\XRPLParserBase;

final class EscrowCreate extends XRPLParserBase
{
  private array $acceptedParsedTypes = ['SENT','RECEIVED','UNKNOWN'];

  /**
   * Parses EscrowCreate type fields and maps them to $this->data
   * Balanche changes only happen when account creates check (pays fee), on destination balance changes when CheckCash event is executed.
   * @see https://xrpl.org/transaction-types.html
   * @see 4E0AA11CBDD1760DE95B68DF2ABBE75C9698CEB548BEA9789053FCB3EBD444FB - rf1BiGeXwwQoi8Z2ueFYTEXSwuJYfV2Jpn - creator
   * @see 4E0AA11CBDD1760DE95B68DF2ABBE75C9698CEB548BEA9789053FCB3EBD444FB - ra5nK24KXen9AHvsdFTKHSANinZseWnPcX - destination
   * @see DF2BDFCAA19B9938A6DBE0070F6466E4649B9988DE7D7558CE71FFFAABAE5D48 - UNKNOWN xMART...
   * @return void
   */
  protected function parseTypeFields(): void
  {
    $parsedType = $this->data['txcontext'];
    if(!in_array($parsedType, $this->acceptedParsedTypes))
      throw new \Exception('Unhandled parsedType ['.$parsedType.'] on EscrowCreate with HASH ['.$this->data['hash'].'] and perspective ['.$this->reference_address.']');

    if($parsedType == 'SENT') {
      $this->data['Counterparty'] = $this->tx->Destination;
      $this->data['In'] = false;
    } elseif($parsedType == 'RECEIVED') {
      $this->data['Counterparty'] = $this->tx->Account;
      $this->data['In'] = true;
    } elseif($parsedType == 'UNKNOWN') {
      $this->data['Counterparty'] = $this->tx->Account;
      $this->data['In'] = false;
      $this->persist = false;
    }

    # Balance changes from eventList (primary/secondary, both, one, or none)
    if(isset($this->data['eventList']['primary'])) {
      $this->data['Amount'] = $this->data['eventList']['primary']['value'];
      if($this->data['eventList']['primary']['currency'] !== 'XRP') {
        throw new \Exception('Unhandled non XRP value on EscrowCreate with HASH ['.$this->data['hash'].'] and perspective ['.$this->reference_address.']');
      }
    }
  }

  /**
   * Returns standardized array of relevant data for storing to Dynamo database.
   * key => value one dimensional array which correlates to column => value in DyDb.
   * @return array
   */
  public function toBArray(): array
  {
    $r = [
      't' => ripple_epoch_to_carbon((int)$this->data['Date'])->format('Y-m-d H:i:s.uP'),
      'l' => $this->data['LedgerIndex'],
      'li' => $this->data['TransactionIndex'],
      'isin' => $this->data['In'],
      'r' => (string)$this->data['Counterparty'],
      'h' => (string)$this->data['hash'],
      'offers' => [],
      'nftoffers' => [],
      'hooks' => $this->data['hooks'],
    ];

    if(\array_key_exists('Amount', $this->data))
      $r['a'] = $this->data['Amount'];

    if(\array_key_exists('Fee', $this->data))
      $r['fee'] = $this->data['Fee'];

    return $r;
  }
}