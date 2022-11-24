<?php

namespace App\Repository;

class AccountsRepository
{
  /**
   * Load account data by address.
   * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/bigquery/api/src
   * @return ?array
   */
  public static function fetchByAddress(string $address): ?array
  {
    return self::fetchOne('address = "'.$address.'"');
  }

  /**
   * Fetches one record from database.
   * @return ?array
   */
  public static function fetchOne($where): ?array
  {
    $bq = app('bigquery');
    $query = 'SELECT address,l FROM `'.config('bigquery.project_id').'.xwa.accounts` WHERE '.$where.' LIMIT 1';
    $results = $bq->runQuery($bq->query($query));
    $r = null;
    foreach ($results as $row) {
      $r = $row;
      break;
    }
    return $r;
  }

  /**
   * Inserts one record to accounts table.
   * @return bool true on success
   */
  public static function insert(array $values): bool
  {
    if(!count($values))
      throw new \Exception('Values missing');

    $insert ='INSERT INTO `'.config('bigquery.project_id').'.xwa.accounts` ('.\implode(',',\array_keys($values)).') VALUES (';
    $i = 0;
    foreach($values as $v) {
      if($i > 0) $insert .= ',';
      if(\is_string($v))
        $insert .= '"'.$v.'"';
      else
        $insert .= $v;
      $i++;
    }
    $insert .= ')';
    
    $bq = app('bigquery');
    $dataset = $bq->dataset('xwa');
    $query = $bq->query($insert)->defaultDataset($dataset);
    try {
      $bq->runQuery($query);
    } catch (\Throwable $e) {
      //dd($e->getMessage());
      throw $e;
      return false;
    }
    return true;
  }
}