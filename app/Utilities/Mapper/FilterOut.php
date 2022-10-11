<?php

namespace App\Utilities\Mapper;

use Illuminate\Support\Facades\Cache;
use App\Models\Map;
use App\Models\Ledgerindex;

class FilterOut extends FilterBase {
    
  private readonly string $address;
  private readonly array $foundLedgerIndexesIds;
  private readonly array $conditions;
  private array $allowedTxTypes = ['Payment','Trustset'];
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
   * Returns array with count information for this filter.
   * @return array
   */
  public function reduce(): array
  {
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
          $count = $this->fetchCount($ledgerindexEx[0], $ledgerindexEx[1], $txTypeNamepart, $countTotalReduced['first'], $countTotalReduced['last']);

          if($count > 0) { //has transactions
            $r[$txTypeNamepart][$ledgerindex] = [
              'total' => $countTotalReduced['total'],
              'found' => $count,
              'e' => self::calcEqualizer($countTotalReduced['e'], 'eq'),
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

  private function conditionName()
  {
    return 'dirout';
  }

  /**
   * @param int $ledgerindex - local internal LedgerIndex->id
   * @param int $subpage - subpage within LedgerIndex
   * @param string $txTypeNamepart
   * @param ?int $first - SK*10000 (inclusive)
   * @param ?int $last - SK*10000 (inclusive)
   */
  private function fetchCount(int $ledgerindex, int $subpage, string $txTypeNamepart, ?int $first, ?int $last): int
  {
    $DModelName = '\\App\\Models\\DTransaction'.$txTypeNamepart;
    $cond = $this->conditionName();
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
        $model = $DModelName::where('PK',$this->address.'-'.$DModelName::TYPE);
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

        $model = $model->where('SK','between',[ ($first/10000), ($last/10000) ]); //inclusive
        $model = $this->applyQueryCondition($model); //check value presence (in attribute always true if in)
        $count = \App\Utilities\PagedCounter::count($model);

        $map = new Map;
        $map->address = $this->address;
        $map->ledgerindex_id = $ledgerindex;
        $map->txtype = $DModelName::TYPE;
        $map->condition = $cond;
        $map->count_num = $count;
        $map->page = $subpage;
        $map->created_at = \date('Y-m-d H:i:s');
        $map->save();
      }
      $r = $map->count_num;
      Cache::put( $cache_key, $r, 2629743); //2629743 seconds = 1 month
    }
    return $r;
  }

  /**
   * Check if DyDB item has $value in its data.
   * Checked field depends on filter.
   * @return bool
   */
  public static function itemHasFilter(\App\Models\DTransaction $item, string|int|float|bool $value): bool
  {
    if(!isset($item->in)) return true;
    return (isset($item->in) && !$item->in);
  }

  /**
   * Adds WHERE conditions to query builder if any.
   * @return \BaoPham\DynamoDb\DynamoDbQueryBuilder
   */
  public function applyQueryCondition(\BaoPham\DynamoDb\DynamoDbQueryBuilder $query, ...$params)
  {
    return $query->whereNull('in');
  }

}