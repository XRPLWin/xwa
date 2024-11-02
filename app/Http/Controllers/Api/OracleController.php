<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use XRPLWin\XRPL\Client as XRPLWinApiClient;
use App\Models\Oracle;
use Illuminate\Support\Facades\Validator;

class OracleController extends Controller
{

  /**
   * Legacy oracle (will work on xahau due to XRPLLabs)
   * @see https://github.com/XRPL-Labs/XRPL-Persist-Price-Oracle/blob/main/README.md
   */
  public function usd()
  {
    $account_lines = app(XRPLWinApiClient::class)->api('account_lines')
      ->params([
          'account' => 'rXUMMaPpZqPutoRszR29jtC8amWq3APkx',
          'limit' => 1
      ]);

    try {
      $account_lines->send();
    } catch (\XRPLWin\XRPL\Exceptions\XWException $e) {
      // Handle errors
      throw $e;
    }

    $lines = (array)$account_lines->finalResult();

    $ttl = 300; //5 mins
    $httpttl = 300; //5 mins

    return response()->json(['price' => $lines[0]->limit])
      ->header('Cache-Control','public, s-max-age='.$ttl.', max_age='.$httpttl)
      ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $httpttl))
    ;
  }


  public function oracles(Request $request)
  {
    $ttl = 60; //1 min
    $httpttl = 60; //1 min
    $limit = 200; //200
    $page = (int)$request->input('page');
    if(!$page) $page = 1;
    $hasMorePages = false;
    $order = 'updated_at'; //reserved (not used yet)
    $direction = $request->input('direction');
    $direction = $direction == 'asc' ? 'asc':'desc';
    
    $validator = Validator::make([
      'page' => $page,
      'oracle' => $request->input('oracle'),
      'provider' => $request->input('provider'),
      'base' => $request->input('base'),
      'quote' => $request->input('quote'),
    ], [
      'page' => 'required|int',
      'oracle' => ['nullable',new \App\Rules\XRPAddress, 'alpha_num:ascii'],
      'provider' => 'nullable|string|alpha_num:ascii',
      'base' => 'nullable|string|alpha_num:ascii',
      'quote' => 'nullable|string|alpha_num:ascii',
    ]);

    if($validator->fails())
      abort(422, 'Input parameters are invalid');

    $oracles = Oracle::select('oracle','provider','base','quote','last_value','updated_at')
      ->orderBy($order,$direction)
      ->limit($limit);

    if($request->input('oracle')) {
      $oracles = $oracles->where('oracle',$request->input('oracle'));
    }

    if($request->input('provider')) {
      $oracles = $oracles->where('provider',$request->input('provider'));
    }

    if($request->input('base')) {
      $oracles = $oracles->where('base',$request->input('base'));
    }

    if($request->input('quote')) {
      $oracles = $oracles->where('quote',$request->input('quote'));
    }
    $num_results = $oracles->count();

    $pages = (int)\ceil($num_results / $limit);
    if($pages < 1) $pages = 1;
    if($page > $pages)
      abort(404);

    if($num_results > $limit*$page)
      $hasMorePages = true;

    $oracles = $oracles->orderBy($order,$direction)->limit($limit)->get();
    $r = [];
    foreach($oracles as $row) {
      //dd($row);
      $r[] = [
        'oracle' => $row->oracle,
        'provider' => $row->provider,
        'base' => $row->base,
        'quote' => $row->quote,
        'base_formatted' => xrp_currency_to_symbol($row->base),
        'quote_formatted' => xrp_currency_to_symbol($row->quote),
        'last_value' => $row->last_value,
        'timestamp' => $row->updated_at->format('U'),
      ];
    }

    //dd($oracles);

    return response()->json([
      'success' => true,
      'page' => $page,
      'pages' => $pages,
      'more' => $hasMorePages,
      'total' => $num_results,
      'data' => $r,
    ])
      ->header('Cache-Control','public, s-max-age='.$ttl.', max_age='.$httpttl)
      ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $httpttl))
    ;
  }
}
