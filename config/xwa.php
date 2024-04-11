<?php
/**
 * XRPLWinAnalyzer (XWA) main config file.
 */

$r = [
  
  'version' => '0.3.4',

  /*
  |--------------------------------------------------------------------------
  | Database engine
  |--------------------------------------------------------------------------
  | Default: sql
  | Options: sql,bigquery
  */
  'database_engine' => env('XWA_DATABASE_ENGINE', 'sql'),

  /*
  |--------------------------------------------------------------------------
  | Database engine use ROCKSDB when first creating database tables
  |--------------------------------------------------------------------------
  | If true, it will create SQL database tables with ROCKSDB storage engine.
  | If false, it will omit rocksdb rule, and tables will be created with
  |   default storage engine defined in SQL.
  | Default: true
  | Options: true,false
  */
  'database_engine_userocksdb' => (bool) env('XWA_DATABASE_ENGINE_USEROCKSDB', true),

  /*
  |--------------------------------------------------------------------------
  | Sync type
  |--------------------------------------------------------------------------
  | Default: account
  | Options: account,continuous
  */
  'sync_type' => env('XWA_SYNC_TYPE', 'account'),
  'sync_type_continuous' => [

    //Number of parallel processes that will be spawned
    'processes' => env('XWA_SYNC_TYPE_CONTINUOUS_PROCESSES', 4),

    //How much ledgers to process before global commit and process end
    //Set this value to 50 when system is in sync, 1000 when system is behind
    //Warning: Do not change this value when there are unfinished processes! (check table synctrackers)
    'ledgersperprocess' => env('XWA_SYNC_TYPE_CONTINUOUS_LEDGERSPERPROCESS', 50),
  ],

  /*
  |--------------------------------------------------------------------------
  | Search limit per page
  |--------------------------------------------------------------------------
  | Default: 500
  | How much results to pull before paginating.
  */
  'limit_per_page' => env('XWA_SEARCH_LIMIT', 500),

  /*
  |--------------------------------------------------------------------------
  | Transaction types list
  |--------------------------------------------------------------------------
  | Autogenerated from codebase.
  | List of transaction types, keyed by type integer.
  */
  'transaction_types' => [
    //<DTransaction::TYPE> => Typenamepart,
    //1 => 'Payment',
    //2 => 'Activation',
    //...
  ],
  
  /*
  |--------------------------------------------------------------------------
  | XRPL Address characters
  |--------------------------------------------------------------------------
  | Characters that can be in address.
  | DynamoDB transaction databases are created according to thiese characters.
  */
  //'address_characters' => [
  //  //letters lowercase:
  //  'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
  //  //numbers:
  //  '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
  //],

  'queue_groups' => [
    'q1' => ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l'],
    'q2' => ['m', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x'],
    'q3' => ['y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
  ],

  'queue_groups_reversed' => [
    //Autogenerated
  ],
];

//generate transaction_types
$dir = __DIR__.'/../app/Models';
$dirlist = \scandir($dir);
foreach($dirlist as $v) {
  if(\str_starts_with($v,'BTransaction') && $v !== 'BTransaction.php' && \str_ends_with($v,'.php')) {
    $modelname = '\\App\\Models\\'.\substr($v,0,-4);
    if(isset($r['transaction_types'][$modelname::TYPE])) {
      throw new \Exception('Misconfigured models - duplicate TYPE '.$modelname::TYPE);
    }
    $r['transaction_types'][$modelname::TYPE] = \substr(\substr($v,0,-4),12);
  }
}

\ksort($r['transaction_types'],SORT_NUMERIC);


//generate queue_groups_reversed
foreach($r['queue_groups'] as $name => $v_chars) {
  foreach($v_chars as $v_char) {
    $r['queue_groups_reversed'][$v_char] = $name;
  }
}

return $r;
