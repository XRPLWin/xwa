<?php

namespace App\Models;
#use Illuminate\Support\Facades\DB;
use App\Repository\UnlvalidatorsRepository;
use Illuminate\Support\Collection;
use XRPLWin\UNLReportReader\UNLReportReader;

class BUnlvalidator extends B
{
  protected $table = 'unlvalidators';
  public $timestamps = false;
  protected $primaryKey = 'validator';
  protected $keyType = 'string';
  const repositoryclass = UnlvalidatorsRepository::class;

  public $fillable = [
    'validator', //Primary Key
    'account',
    'first_l',
    'last_l',
    'current_successive_fl_count',
    'max_successive_fl_count',
    'active_fl_count'
  ];

  protected $casts = [
    //'validators' => 'array',
  ];

  const BQCASTS = [
    'validator' => 'STRING',
    'account'  => 'STRING',
    'first_l' => 'INTEGER',
    'last_l' => 'INTEGER',
    'current_successive_fl_count' => 'INTEGER',
    'max_successive_fl_count' => 'INTEGER',
    'active_fl_count' => 'INTEGER',
  ];

  protected function bqPrimaryKeyCondition(): string
  {
    return 'validator = """'.$this->validator.'"""';
  }

  public static function find(int $validator, ?string $select = null): ?self
  {
    $data = UnlvalidatorsRepository::fetchByValidator($validator,$select);
    
    if($data === null)
      return null;
    return self::hydrate([$data])->first();
  }

  public static function fetchAll(?string $select = null): Collection
  {
    $data = UnlvalidatorsRepository::fetchAll($select);
    $models = [];
    foreach($data as $v) {
      $models[] = self::hydrate([$v])->first();
    }
    return collect($models);
  }

  public static function insert(array $values): ?BUnlreport
  {
    $saved = UnlvalidatorsRepository::insert($values);
    if($saved)
      return self::hydrate([$values])->first();
    return null;
  }

  /**
   * @param int $max_ledger_index - this is max ledger index statistics will check to
   * Returned values:
   * - reliability - int percent how much was online since first discovered eg 12.558 (3 decimal places)
   * @return array ['reliability' => float percent]
   */
  public function getStatistics(int $max_ledger_index): array
  {
    $r = [
      'reliability' => 0,
      //'last_active_l' => $this->last_l, //this is last active ledger (flag ledger)
      //'max_successive_fl_count' => $this->max_successive_fl_count
    ];
    $total_flag_ledgers = UNLReportReader::calcNumFlagsBetweenLedgers($this->first_l,$max_ledger_index);
    $r['reliability'] = calcPercentFromTwoNumbers($this->active_fl_count,$total_flag_ledgers,3);

    
    //dd($r,$this->first_l,$this->active_fl_count,$total_flag_ledgers);
    //dd('stats');
    return $r;
  }

}