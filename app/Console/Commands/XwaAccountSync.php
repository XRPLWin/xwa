<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

use XRPLWin\XRPL\Client;
use App\Utilities\AccountLoader;
use App\Utilities\Ledger;
use App\Models\BAccount;
use App\Models\BTransactionActivation;
use App\Models\Ledgerindex;
use App\Models\Map;
use App\XRPLParsers\Parser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Repository\Batch;

class XwaAccountSync extends Command
{
    /**
     * The name and signature of the console command.
     * @sample php artisan xwa:accountsync rAcct...
     * @var string
     */
    protected $signature = 'xwa:accountsync
                            {address : XRP account address}
                            {--recursiveaccountqueue : Enable to create additional queues for other accounts}
                            {--limit=0 : Limit batch jobs, if limit is set after x batch jobs new queue job will be created, leave empty for no limit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do a full sync of account';

    /**
     * Flag if this scan will generate new queues.
     *
     * @var bool
     */
    protected bool $recursiveaccountqueue = false;

    /**
     * How much batch jobs will be executed before requeuing
     * Requeuing will move job to end of queue.
     * 
     * @var bool
     */
    protected int $batchlimit = 0;

    /**
     * Current batch being executed.
     *
     * @var bool
     */
    protected int $batch_current = 0;

    /**
     * Current ledger being scanned.
     *
     * @var bool
     */
    private int     $ledger_current = -1;
    private string  $ledger_current_time = '';

    /**
     * XRPL API Client instance
     *
     * @var \XRPLWin\XRPL\Client
     */
    protected Client $XRPLClient;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $this->XRPLClient = app(Client::class);

      //dd('test',config_static('xrpl.address_ignore.rBKPS4oLSaV2KVVuHH8EpQqMGgGefGFQs72'));
      $address = $this->argument('address');
      $this->recursiveaccountqueue = $this->option('recursiveaccountqueue'); //bool
      $this->batchlimit = (int)$this->option('limit'); //int
      
      //$this->ledger_current = $this->XRPLClient->api('ledger_current')->send()->finalResult();
      
      $this->ledger_current = Ledger::current();
      $this->ledger_current_time = \Carbon\Carbon::now()->format('Y-m-d H:i:s.uP');
     
      //clear account cache
      Cache::forget('daccount:'.$address);
      Cache::forget('daccount_fti:'.$address);
      
      $account = AccountLoader::getOrCreate($address);
      
      //dd($account);
      //dd($account);
      //If this account is issuer (by checking obligations) set t field to 1.
      //if($account->checkIsIssuer())
      //  $account->t = 1;
      //else
      //  unset($account->t); //this will remove field on model save

      //dd($account);
      
      //Test only start (comment this)
      //$account->l = 73806933;$account->save();exit;
      //$account->l = 74139050;$account->save();exit;
      //Test only end

      //$this->ledger_current = 66055480;
     // $account->l = 66055470;
      
      /*if( config_static('xrpl.address_ignore.'.$account->address) !== null ) {
        $this->info('History sync skipped (ignored)');
        //modify $account todo
        return 0;
      }*/

      $account_tx = $this->XRPLClient->api('account_tx')
          ->params([
            'account' => $account->address,
            //'ledger_index' => 'current',
            'ledger_index_min' => (int)$account->l, //Ledger index this account is scanned to.
            'ledger_index_max' => $this->ledger_current,
            'binary' => false,
            'forward' => true,
            'limit' => 400, //400
          ]);
         
      $account_tx->setCooldownHandler(
        /**
         * @param int $current_try Current try 1 to max
         * @param int $default_cooldown_seconds Predefined cooldown seconds
         * @see https://github.com/XRPLWin/XRPL#custom-rate-limit-handling
         * @return void|boolean return false to stop process
         */
        function(int $current_try, int $default_cooldown_seconds) {
            $sec = $default_cooldown_seconds * $current_try;
            if($sec > 300) {
              echo 'Cooldown threshold of '.$current_try.' tries reached exiting'.PHP_EOL;
              return false; //force stop
            }
            echo 'Cooling down for '.$sec.' seconds'.PHP_EOL;
            sleep($sec);
        }
      );
          
      $do = true;
      $isLast = true;
      $dayToFlush = null;
      $i = 0;
      while($do) {
        $i++;
        try {
          $account_tx->send();
        } catch (\XRPLWin\XRPL\Exceptions\XWException $e) {
          // Handle errors
          $this->info('');
          $this->info('Error catched: '.$e->getMessage());
          //throw $e;
        }
        //if($i ==8) dd($account_tx);
        //Handles sucessful response from ledger with unsucessful message.
        //Hanldes rate limited responses.
        $is_success = $account_tx->isSuccess();

        if(!$is_success) {
          $this->info('');
          $this->info('Unsuccessful response (code 1), trying again: Ledger from '.(int)$account->l.' to '.$this->ledger_current);
          $this->info('Retrying in 3 seconds...');
          sleep(3);
        }
        else
        {
          $this->batch_current++;
          //dd($account_tx);
          
          $txs = $account_tx->finalResult();
          $this->info('');
          $this->info('Starting batch of '.count($txs).' transactions: Ledger from '.(int)$account->l.' to '.$this->ledger_current);
          $bar = $this->output->createProgressBar(count($txs));
          $bar->start();
          
          $parsedDatas = []; //list of sub-method results (parsed transactions)

          # Prepare Batch instance which will hold list of queries to be executed at once to BigQuery
          $batch = new Batch;

          
          # Parse each transaction and prepare batch execution queries
          foreach($txs as $tx) {
            $parsedDatas[] = $this->processTransaction($account,$tx, $batch);
            $bar->advance();
          }
          unset($txs);

          # Execute batch queries
          $this->info('');
          $this->info('Executing batch of queries...');
          $processed_rows = $batch->execute();
          unset($batch);

          $this->info('- DONE (processed '.$processed_rows.' rows)');
          //$this->info('Process memory usage: '.memory_get_usage_formatted());

          # Post processing results (flush cache)
          foreach($parsedDatas as $parsedData) {
            if(!empty($parsedData)) {
              if($dayToFlush === null) {
                
                $dayToFlush = bqtimestamp_to_carbon($parsedData['t'])->format('Y-m-d');
                $this->info('Flushing day (initial) '.$dayToFlush);
                $this->flushDayCache($account->address,$dayToFlush);
              } else {
                $txDayToFlush = bqtimestamp_to_carbon($parsedData['t'])->format('Y-m-d');
                if($dayToFlush !== $txDayToFlush) {
                  $this->info('Flushing day '.$dayToFlush);
                  $this->flushDayCache($account->address,$dayToFlush);
                  unset($dayToFlush);
                  $dayToFlush = $txDayToFlush;
                }
              }
            }
          }
          unset($parsedDatas);
          
          $bar->finish();
          unset($bar);

          $next = $account_tx->next();
          unset($account_tx);
         
          if($next !== null) {
            $account_tx = $next;
            unset($next);
            //update last synced ledger index to account metadata
            $account->l = $tx->tx->ledger_index;
            $account->lt = ripple_epoch_to_carbon($tx->tx->date)->format('Y-m-d H:i:s.uP');
            //$this->info($tx->tx->ledger_index.' is last ledger');
            $account->save();
            //continuing to next page
          }
          else
            $do = false;

          if($do) {
            if($this->batchlimit > 0 && $this->batch_current >= $this->batchlimit) {
              # batch limit reached
              $do = false; //stop
              $isLast = false; //flat it is not last run
              $this->info('');
              $this->info('Batch limit ('.$this->batch_current.') reached, requeuing job');
              $account->sync($this->recursiveaccountqueue, true, $this->batchlimit);
              sleep(1);
            }
          }
        }
      }

      # Save last scanned ledger index
      if($isLast) {
        $account->l = $this->ledger_current;
        $account->lt = $this->ledger_current_time;
        //dd($account);
        $account->save();
      }
      
      //TODO start data analysis
      //$analyzed = StaticAccount::analyzeData($account);

      return Command::SUCCESS;
    }


    /**
     * Calls appropriate method.
     * @return ?array
     */
    private function processTransaction(BAccount $account, \stdClass $transaction, Batch $batch): ?array
    {
      $type = $transaction->tx->TransactionType;
      $method = 'processTransaction_'.$type;
      
      
      if($transaction->meta->TransactionResult != 'tesSUCCESS')
        return null; //do not log failed transactions

      //this is faster than call_user_func()
      return $this->{$method}($account, $transaction, $batch);
    }

    /**
    * Executed offer
    */
    private function processTransaction_OfferCreate(BAccount $account, \stdClass $transaction): array
    {
      return  []; //TODO
      $txhash = $tx['hash'];

      $TransactionOfferCheck = TransactionTrustset::where('txhash',$txhash)->count();
      if($TransactionOfferCheck)
        return null; //nothing to do, already stored




      if(isset($meta['AffectedNodes']) && is_array($meta['AffectedNodes'])) {

        foreach($meta['AffectedNodes'] as $anode) {

          if(isset($anode['ModifiedNode']))
          {

          //  if(!isset($anode['ModifiedNode']['FinalFields']['Account'] ))
          //    dd($anode);

          //if($anode['ModifiedNode']['LedgerEntryType'] == 'AccountRoot' && !isset($anode['ModifiedNode']['FinalFields']))
          //  dd($anode);

            if($anode['ModifiedNode']['LedgerEntryType'] == 'AccountRoot' &&
                isset($anode['ModifiedNode']['FinalFields']) &&
                $anode['ModifiedNode']['FinalFields']['Account'] == $account->account) {
              $newBalance = (int)$anode['ModifiedNode']['FinalFields']['Balance']; //drops
              $oldBalance = (int)$anode['ModifiedNode']['PreviousFields']['Balance']; //drops
              $gainLossXrp = (($newBalance - $oldBalance)+$tx['Fee']) / 1000000;

              $TransactionOffer = new TransactionOffer;
              $TransactionOffer->txhash = $txhash;
              $TransactionOffer->account_id = $account->id;
              $TransactionOffer->fee = $tx['Fee']; //in drops
              $TransactionOffer->time_at = ripple_epoch_to_carbon($tx['date']);
              $TransactionOffer->amount = $gainLossXrp;
              if($TransactionOffer->amount !== 0)
                $TransactionOffer->save();

              //dd($txhash,$newBalance,$oldBalance,($newBalance - $oldBalance));
            }
            //dd($anode);
          }
        }
      }
    }

    private function processTransaction_OfferCancel(BAccount $account, \stdClass $transaction): array
    {
      return []; //TODO
    }

    /**
    * Payment to or from in any currency.
    * @modifies DTransaction $account
    * @return void
    */
    //OK
    private function processTransaction_Payment(BAccount $account, \stdClass $transaction, Batch $batch):array
    {
      /** @var \App\XRPLParsers\Types\Payment */
      $parser = Parser::get($transaction->tx, $transaction->meta, $account->address);
      $parsedData = $parser->toDArray();

      $TransactionClassName = '\\App\\Models\\BTransaction'.$parser->getTransactionTypeClass();
      $model = new $TransactionClassName($parsedData);
      $model->address = $account->address;
      $model->xwatype = $TransactionClassName::TYPE;
      $batch->queueModelChanges($model);
      //$model->save();
      
      # Activations by payment:
      $parser->detectActivations();

      if($activatedAddress = $parser->getActivated()) {
        //$this->info('');
        //$this->info('Activation: '.$activatedAddress. ' on index '.$parser->SK());
        $Activation = new BTransactionActivation([
          'address' => $account->address,//.'-'.BTransactionActivation::TYPE,
          'xwatype' => BTransactionActivation::TYPE,
          'h' => $parsedData['h'],
          't' => ripple_epoch_to_carbon((int)$parser->getDataField('Date'))->format('Y-m-d H:i:s.uP'),
          'r' => $activatedAddress,
          'isin' => true,
        ]);
        $batch->queueModelChanges($Activation);
        //$Activation->save();
      }

      if($activatedByAddress = $parser->getActivatedBy()) {
        $this->info('');
        $this->info('Activation: Activated by '.$activatedByAddress. ' on hash '.$parser->getData()['hash']);
        $account->activatedBy = $activatedByAddress;
        $account->isdeleted = false; //in case it is reactivated, this will remove field on model save
        $batch->queueModelChanges($account);
        //$account->save();

        if($this->recursiveaccountqueue)
        {
          //parent created this account, queue parent
          $this->info('Queued account: '.$activatedByAddress. ' on hash '.$parser->getData()['hash']);
          //$source_account->sync(true);
          $newAccount = AccountLoader::getOrCreate($activatedByAddress);
          $newAccount->sync(
            $this->recursiveaccountqueue,
            false,
            $this->batchlimit
          );
        }
      }
      return $parsedData;
    }

    //OK
    private function processTransaction_TrustSet(BAccount $account, \stdClass $transaction, Batch $batch): array
    {
      /** @var \App\XRPLParsers\Types\TrustSet */
      $parser = Parser::get($transaction->tx, $transaction->meta, $account->address);

      $parsedData = $parser->toDArray();

      $TransactionClassName = '\\App\\Models\\BTransaction'.$parser->getTransactionTypeClass();
      $model = new $TransactionClassName($parsedData);
      $model->address = $account->address;
      $model->xwatype = $TransactionClassName::TYPE;
      $batch->queueModelChanges($model);
      //$model->save();
      return $parsedData;
    }

    private function processTransaction_AccountSet(BAccount $account, \stdClass $transaction): array
    {
      return []; //not used yet

      $txhash = $tx['hash'];
      $TransactionAccountsetCheck = TransactionAccountset::where('txhash',$txhash)->count();
      if($TransactionAccountsetCheck)
        return []; //nothing to do, already stored

      $TransactionAccountset = new TransactionAccountset;
      $TransactionAccountset->txhash = $txhash;

      if($account->account == $tx['Account'])
        $TransactionAccountset->source_account_id = $account->id; //reuse it
      else
        $TransactionAccountset->source_account_id = StaticAccount::GetOrCreate($tx['Account'],$this->ledger_current)->id;

      $TransactionAccountset->fee = $tx['Fee']; //in drops
      $TransactionAccountset->time_at = ripple_epoch_to_carbon($tx['date']);

      //$TransactionAccountset->set_flag = $tx['SetFlag'];

      $TransactionAccountset->save();



      return [];
    }

    //OK
    private function processTransaction_AccountDelete(BAccount $account, \stdClass $transaction, Batch $batch): array
    {
      /** @var \App\XRPLParsers\Types\AccountDelete */
      $parser = Parser::get($transaction->tx, $transaction->meta, $account->address);

      $parsedData = $parser->toDArray();

      $TransactionClassName = '\\App\\Models\\BTransaction'.$parser->getTransactionTypeClass();
      $model = new $TransactionClassName($parsedData);
      $model->address = $account->address;
      $model->xwatype = $TransactionClassName::TYPE;
      $batch->queueModelChanges($model);
      //$model->save();

      if(isset($parsedData['in']) && $parsedData['in']) {
        //incomming xrp
      } else {
        //outgoing, this is deleted account, flag account deleted
        $this->info('');
        $this->info('Deleted');
        $account->isdeleted = true;
        $batch->queueModelChanges($account);
        //$account->save();
        //dd($account);
        
      }

      return $parsedData;
    }

    private function processTransaction_SetRegularKey(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_SignerListSet(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_CheckCancel(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_CheckCash(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_CheckCreate(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }


    private function processTransaction_EscrowCreate(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_EscrowFinish(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_EscrowCancel(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_PaymentChannelCreate(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_PaymentChannelFund(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_PaymentChannelClaim(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_DepositPreauth(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_TicketCreate(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_NFTokenAcceptOffer(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_NFTokenBurn(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_NFTokenCancelOffer(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_NFTokenCreateOffer(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    private function processTransaction_NFTokenMint(BAccount $account, \stdClass $transaction): array
    {
      return [];
    }

    /**
     * Flush account maps and cache for this day.
     * @deprecated
     */
    private function flushDayCache(string $address, string $day)
    {
      /*
      $carbon = Carbon::createFromFormat('Y-m-d',$day);
      $liId = Ledgerindex::getLedgerindexIdForDay($carbon);
      if($liId) {
        //flush from cache
        $search = Map::select('page','condition','txtype')->where('address',$address)->where('ledgerindex_id',$liId)->get();
        foreach($search as $v) {
          $cache_key = 'mpr'.$address.'_'.$v->condition.'_'.$liId.'_'.$v->page.'_'.$v->txtype;
          Cache::forget($cache_key);
        }
        //flush from maps table
        Map::where('address',$address)->where('ledgerindex_id',$liId)->delete();
      }*/
    }
}
