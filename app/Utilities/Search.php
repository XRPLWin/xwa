<?php

namespace App\Utilities;

use App\Models\Ledgerindex;
#use App\Utilities\Mapper;
#use XRPLWin\XRPL\Client;
#use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Search
{
  private string $address;
  private readonly array $result;
  private readonly array $params;
  private bool $isExecuted = false;
  private array $parametersWhitelist = ['from','to','dir'];
  private array $txTypes = [
    // App\Models\DTransaction<VALUE_BELOW>::TYPE => App\Models\DTransaction<VALUE_BELOW>
    1 => 'Payment',
    2 => 'Activation',
    3 => 'Trustset',
    // ...todo
  ];

  public function __construct(string $address)
  {
    $this->address = $address;
  }

  public function buildFromArray(array $data): self
  {
   
    ///($data);
    foreach($data as $k => $v)
    {
      //
    }
    $this->params = $data;
    return $this;
  }

  public function buildFromRequest(Request $request): self
  {
    return $this->buildFromArray($request->only($this->parametersWhitelist));
  }

  public function execute(): self
  {
    $this->result = [];

    //Mapper
    $mapper = new Mapper();
    $mapper->setAddress($this->address);

    //TODO do not allow more than 31 days at once!!! for performace reasons
    $mapper
    ->addCondition('from',$this->param('from'))
    ->addCondition('to',$this->param('to'));
    

    $txTypes = $this->txTypes; //thiese are all types
    //Todo get types from param

    $mapper->addCondition('txTypes',$txTypes);

    $param_dir = $this->param('dir');
    if($param_dir && ($param_dir == 'in' || $param_dir == 'out')) {
      $mapper->addCondition('dir',$param_dir);
    }
    unset($param_dir);


    $intersected = $mapper->getIntersectedLedgerindexes();
    dd($mapper,$intersected);

    $this->isExecuted = true;
    return $this;
    exit;







    # 1. Get time range from-to
    if(!$this->param('from') || !$this->param('to')) abort(422);
    try{
      $from = Carbon::createFromFormat('Y-m-d', $this->param('from'));
      $to = Carbon::createFromFormat('Y-m-d', $this->param('to'));
    } catch (\Throwable) {
      abort(422, 'Unable to parse from or to YYYY-MM-DD format');
    }

    //TODO check if from is less or equal than to else abort 422
    
    $fromLedger = Ledgerindex::select('id','ledger_index_first','day')->where('day',$from)->first();
    if(!$fromLedger) abort(422, 'From ledger out of range');
    $toLedger = Ledgerindex::select('id','ledger_index_last','day')->where('day',$to)->first();
    if(!$toLedger) abort(422, 'To ledger out of range');
    //dd($fromLedger,$toLedger);

    $mapper
      ->addCondition('from',$fromLedger)
      ->addCondition('to',$toLedger);

    # 2. Types to query
    $txTypes = $this->txTypes; //thiese are all types
    //Todo get types from param

    $mapper->addCondition('txTypes',$txTypes);

    $intersected = $mapper->getIntersectedLedgerindexes();
    dd($mapper,$intersected);

    # 2. Get all DTransactions within this range for all types
    $txs = [];
    foreach($txTypes as $txType)
    {
      //dump([$fromLedger->ledger_index_last,$toLedger->ledger_index_last]);
      $mname = '\\App\\Models\\DTransaction'.$txType;
      $txs['T'.$mname::TYPE] = $mname::where('PK',$this->address.'-'.$mname::TYPE)
        ->where('SK','between',[$fromLedger->ledger_index_last,$toLedger->ledger_index_last])
        ->get();
       //dd( $txs['Type'.$mname::TYPE]);
    }
    dd($txs);
    dd($from,$to);









    $this->isExecuted = true;
    return $this;
  }


  ###
  

  public function result(): array
  {
    if(!$this->isExecuted)
      throw new \Exception('Search::result() called before execute()');

    return $this->result;
  }

  private function param($name)
  {
    return isset($this->params[$name]) ? $this->params[$name]:null;
  }
}