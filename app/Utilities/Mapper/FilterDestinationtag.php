<?php

namespace App\Utilities\Mapper;

use Illuminate\Support\Facades\Cache;
use App\Models\Map;
use App\Models\Ledgerindex;

class FilterDestinationtag extends FilterBase {
    
  private readonly string $address;
  private readonly array $foundLedgerIndexesIds;
  private readonly array $conditions;
  private array $allowedTxTypes = ['Payment'];
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
    $FirstFewLetters = \substr($this->conditions['dt'],0,2); //

    $r = [];

    foreach($this->txTypes as $txTypeNamepart) {
      $r[$txTypeNamepart] = [];
      foreach($this->foundLedgerIndexesIds[$txTypeNamepart] as $ledgerindex => $countTotalReduced) {
        $r[$txTypeNamepart][$ledgerindex] = ['total' => $countTotalReduced['total'], 'found' => 0, 'e' => 'eq'];
        if($countTotalReduced['total'] == 0 || $countTotalReduced['found'] == 0) continue; //no transactions here, skip
        

        $count = $this->fetchCount($ledgerindex, $txTypeNamepart, $FirstFewLetters);
        if($count > 0) { //has transactions
          $r[$txTypeNamepart][$ledgerindex] = ['total' => $countTotalReduced['total'], 'found' => $count, 'e' => $this->calcEqualizer($countTotalReduced['e'], 'lte')];
        }
        unset($count);
      }
    }
    return $r;
  }

  private function conditionName(string $FirstFewLetters)
  {
    return 'dt_'.$FirstFewLetters;
  }

  private function fetchCount(int $ledgerindex, string $txTypeNamepart, string $FirstFewLetters): int
  {
    $DModelName = '\\App\\Models\\DTransaction'.$txTypeNamepart;
    $cond = $this->conditionName($FirstFewLetters);
    $cache_key = 'mpr'.$this->address.'_'.$cond.'_'.$ledgerindex.'_'.$DModelName::TYPE;
    $r = Cache::get($cache_key);
    if($r === null) {
      $map = Map::select('count_num')
        ->where('address', $this->address)
        ->where('ledgerindex_id',$ledgerindex)
        ->where('txtype',$DModelName::TYPE)
        ->where('condition',$cond)
        ->first();
  
      if(!$map)
      {
        //no records found, query DyDB for this day, for this type and save
        $li = Ledgerindex::select('ledger_index_first','ledger_index_last')->where('id',$ledgerindex)->first();
        if(!$li) {
          //clear cache then then/instead exception?
          throw new \Exception('Unable to fetch Ledgerindex of ID (previously cached): '.$ledgerindex);
          //return 0; //something went wrong
        }
        $DModelTxCount = $DModelName::where('PK',$this->address.'-'.$DModelName::TYPE)
          ->where('SK','between',[$li->ledger_index_first,$li->ledger_index_last + 0.9999])
          ->where('dt', 'begins_with',$FirstFewLetters) //check value presence (in attribute always does not exists if out)
          ->count();

        $map = new Map;
        $map->address = $this->address;
        $map->ledgerindex_id = $ledgerindex;
        $map->txtype = $DModelName::TYPE;
        $map->condition = $cond;
        $map->count_num = $DModelTxCount;
        $map->created_at = now();
        $map->save();
      }
  
      $r = $map->count_num;
      Cache::put( $cache_key, $r, 2629743); //2629743 seconds = 1 month
    }
    
    return $r;
  }
}