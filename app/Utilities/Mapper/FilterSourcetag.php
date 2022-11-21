<?php

namespace App\Utilities\Mapper;

use Illuminate\Support\Facades\Cache;
use App\Models\Map;
use App\Models\Ledgerindex;

class FilterSourcetag extends FilterBase {
    
  private readonly string $address;
  private readonly array $foundLedgerIndexesIds;
  private readonly array $conditions;
  private array $allowedTxTypes = ['Payment','Payment_Exchange','Payment_BalanceChange'];
  private readonly array $txTypes;

  public function __construct(string $address, array $conditions, array $foundLedgerIndexesIds)
  {
    $this->address = $address;
    $this->conditions = $conditions;
    $this->foundLedgerIndexesIds = $foundLedgerIndexesIds;

    $txTypes = [];
    foreach($this->conditions['txTypes'] as $txType) {
      if(in_array($txType,$this->allowedTxTypes))
        $txTypes[] = $txType;
    }
    $this->txTypes = $txTypes;
  }

  /**
   * 123456... = 12
   * @return string
   */
  public static function parseToNonDefinitiveParam(string $param): string
  {
    return \substr($param,0,2);
  }

  /**
   * Returns array with count information for this filter.
   * @return array
   */
  public function reduce(): array
  {
    $FirstFewLetters = self::parseToNonDefinitiveParam($this->conditions['st']);
    $r = [];
    foreach($this->txTypes as $txTypeNamepart) {
      $r[$txTypeNamepart] = [];
      if(isset($this->foundLedgerIndexesIds[$txTypeNamepart])) {
        foreach($this->foundLedgerIndexesIds[$txTypeNamepart] as $ledgerindex => $countTotalReduced) {
          $r[$txTypeNamepart][$ledgerindex] = [
            'total' => $countTotalReduced['total'],
            'found' => 0,
            'e' => 'eq',
            'first' => $countTotalReduced['first'],
            'last' => $countTotalReduced['last']
          ];
          if($countTotalReduced['total'] == 0 || $countTotalReduced['found'] == 0) continue; //no transactions here, skip
  
          $ledgerindexEx = $this->explodeLedgerindex($ledgerindex);
          $count = $this->fetchCount($ledgerindexEx[0], $ledgerindexEx[1], $txTypeNamepart, $FirstFewLetters, $countTotalReduced['first'], $countTotalReduced['last']);

          if($count > 0) { //has transactions
            $r[$txTypeNamepart][$ledgerindex] = [
              'total' => $countTotalReduced['total'],
              'found' => $count,
              'e' => self::calcEqualizer($countTotalReduced['e'], 'lte'),
              'first' => $countTotalReduced['first'],
              'last' => $countTotalReduced['last']
            ];
          }
          unset($count);
        }
      }
    }
    return $r;
  }

  private function conditionName(string $FirstFewLetters)
  {
    return 'st_'.$FirstFewLetters;
  }

  /**
   * @param int $ledgerindex - local internal LedgerIndex->id
   * @param int $subpage - subpage within LedgerIndex
   * @param string $txTypeNamepart
   * @param string $FirstFewLetters - part of filter to do non-definitive filtering on
   * @param ?int $first - SK*10000 (inclusive)
   * @param ?int $last - SK*10000 (inclusive)
   */
  private function fetchCount(int $ledgerindex, int $subpage, string $txTypeNamepart, string $FirstFewLetters, ?int $first, ?int $last): int
  {
    $DModelName = '\\App\\Models\\DTransaction'.$txTypeNamepart;
    $cond = $this->conditionName($FirstFewLetters);
    $cache_key = 'mpr'.$this->address.'_'.$cond.'_'.$ledgerindex.'_'.$subpage.'_'.$DModelName::TYPE;

    $r = Cache::get($cache_key);
    
    if($r === null) {
      $map = Map::select('count_num')
        ->where('address', $this->address)
        ->where('ledgerindex_id',$ledgerindex)
        ->where('page',$subpage)
        ->where('txtype',$DModelName::TYPE)
        ->where('condition',$cond)
        ->first();
      
      if(!$map)
      {
        $model = $DModelName::createContextInstance($this->address)->where('PK',$this->address.'-'.$DModelName::TYPE);
        if($first == null || $last == null) {
          //Need to get edges
          $li = Ledgerindex::getLedgerindexData($ledgerindex);
          if(!$li) {
            //clear cache then then/instead exception?
            throw new \Exception('Unable to fetch Ledgerindex of ID (previously cached): '.$ledgerindex);
          }
          $first = $first ?? $li[0];
          $last = $last ?? $li[1]; 
        }
        if($last === -1)
          $model = $model->where('SK','>=',($first/10000));
        else
          $model = $model->where('SK','between',[($first/10000),($last/10000)]); //DynamoDB BETWEEN is inclusive
        
        $model = $this->applyQueryCondition($model, $FirstFewLetters);
        //dd($model);
        $count = \App\Utilities\PagedCounter::count($model);

        $map = new Map;
        $map->address = $this->address;
        $map->ledgerindex_id = $ledgerindex;
        $map->txtype = $DModelName::TYPE;
        $map->condition = $cond;
        $map->count_num = $count;
        $map->page = $subpage;
        $map->created_at = now();
        $map->save();
      }
  
      $r = $map->count_num;
      Cache::put( $cache_key, $r, 2629743); //2629743 seconds = 1 month
    }
    
    return $r;
  }

  /**
   * Adds WHERE conditions to query builder if any.
   * @return \BaoPham\DynamoDb\DynamoDbQueryBuilder
   */
  public function applyQueryCondition(\BaoPham\DynamoDb\DynamoDbQueryBuilder $query, ...$params)
  {
    return $query->where('st', 'begins_with',$params[0]);
  }

  /**
   * Check if DyDB item has $value in its data.
   * Checked field is 'st', must be exact.
   * @return bool
   */
  public static function itemHasFilter(\App\Models\DTransaction $item, string|int|float|bool $value): bool
  {
    return ((string)$item->st == (string)$value);
  }
}