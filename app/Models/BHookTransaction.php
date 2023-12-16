<?php

namespace App\Models;

#use Illuminate\Support\Collection;
#use Thiagoprz\CompositeKey\HasCompositeKey;
#use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BHookTransaction extends B
{
  use HasUuids;

  protected $table = 'hook_transactions';
  public $timestamps = false;
  #protected $primaryKey = 'id';
  #protected $primaryKey = ['hook', 'h'];
  #protected $keyType = 'string';
  #public $incrementing = true;

  public static function getRepository(): string
  {
    if(config('xwa.database_engine') == 'bigquery')
      return \App\Repository\Bigquery\HookTransactionsRepository::class;
    else
      return \App\Repository\Sql\HookTransactionsRepository::class;
  }

  public $fillable = [
    'hook', //Primary pt1
    'h', //Primary pt2
    'l',
    't',
    'r',
    'txtype',
    'tec',
    'hookaction', //0 execution, 1 create, 2 destroy, 3 install, 4 uninstall, 5 modify
    'hookresult',
  ];

  protected $casts = [
    't' => 'datetime',
  ];

  const BQCASTS = [
    'hook'    => 'STRING',
    'h'       => 'STRING',
    'l'       => 'INTEGER',
    't'       => 'TIMESTAMP',
    'r'       => 'STRING',
    'txtype'  => 'STRING',
    'tec'  => 'STRING',
    'hookaction' => 'INTEGER',
    'hookresult'  => 'INTEGER',
  ];

  protected function bqPrimaryKeyCondition(): string
  {
    return 'hook = """'.$this->hook.'""" and h = """'.$this->h.'"""';
  }

  /*public static function repo_find(string $hook, int $l_from, bool $lockforupdate = false): ?self
  {
    $data = self::getRepository()::fetchByHookHashAndLedgerFrom($hook,$l_from,$lockforupdate);
    
    if($data === null)
      return null;

    return self::hydrate([$data])->first();
  }

  public static function repo_fetch(string $hook): Collection
  {
    $data = self::getRepository()::fetchByHookHash($hook);
    return self::hydrate($data);
  }

  public function getIsActiveAttribute()
  {
    return $this->l_to === 0;
  }*/

}
