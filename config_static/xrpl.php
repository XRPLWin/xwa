<?php

return [
  /**
  * Ignore history pull on exchange accounts and genesis accounts
  * We do not need to analyze those.
  */
  'genesis_mainnet' => [ //genesis_env(XRPL_NET)
    # GENESIS ACCOUNTS (https://bithomp.com/api/v2/genesis) for XRPL
    'rBKPS4oLSaV2KVVuHH8EpQqMGgGefGFQs7' => 'GENESIS',
    'rLs1MzkFWCxTbuAHgjeTZK4fcCDDnf2KRv' => 'AnnaSee2',
    'rpGaCyHRYbgKhErgFih3RdjJqXDsYBouz3' => 'AnnaSee2',
    'rUnFEsHjxqTswbivzL2DNHBb34rhAgZZZK' => 'DeaDTerra',
    'r4mscDrVMQz2on2px31aV5e5ouHeRPn8oy' => 'CodeShark',
    'rLiCWKQNUs8CQ81m2rBoFjshuVJviSRoaJ' => 'GENESIS',
    'rLCvFaWk9WkJCCyg9Byvwbe9gxn1pnMLWL' => 'bpd',
    'rLzpfV5BFjUmBs8Et75Wurddg4CCXFLDFU' => 'GENESIS',
    'rKZig5RFv5yWAqMi9PtC5akkGpNtn3pz8A' => 'GENESIS',
    'rLeRkwDgbPVeSakJ2uXC2eqR8NLWMvU3kN' => 'GENESIS',
    'rUy6q3TxE4iuVWMpzycrQfD5uZok51g1cq' => 'AmitabhS',
    'r43mpEMKY1cVUX8k6zKXnRhZMEyPU9aHzR' => 'GENESIS',
    'rMwNkcpvcJucoWbFW89EGT6TfZyGUkaGso' => 'GENESIS',
    'r9duXXmUuhSs6JxKpPCSh2tPUg9AGvE2cG' => 'Coinabul',
    'r3AthBf5eW4b9ujLoXNHFeeEJsK3PtJDea' => 'GENESIS',
    'rHC5QwZvGxyhC75StiJwZCrfnHhtSWrr8Y' => 'GENESIS',
    'rPhMwMcn8ewJiM6NnP6xrm9NZBbKZ57kw1' => 'GENESIS',
    'rJ6VE6L87yaVmdyxa9jZFXSAdEFSoTGPbE' => 'riceburner',
    'rDJvoVn8PyhwvHAWuTdtqkH4fuMLoWsZKG' => 'GENESIS',
    'rM1oqKtfh1zgjdAgbFmaRm3btfGBX25xVo' => 'GENESIS',
    'rnp8kFTTm6KW8wsbgczfmv56kWXghPSWbK' => 'mjc',
    'rwpRq4gQrb58N7PRJwYEQaoSui6Xd3FC7j' => 'Transisto',
    'rBJwwXADHqbwsp6yhrqoyt2nmFx9FB83Th' => 'nanotube',
    'rnT9PFSfAnWyj2fd7D5TCoCyCYbK4n356A' => 'X.Trader',
    'rEyhgkRqGdCK7nXtfmADrqWYGT6rSsYYEZ' => 'GENESIS',
    'rPrz9m9yaXT94nWbqEG2SSe9kdU4Jo1CxA' => 'TheTank',
    'rHb9CJAWyB4rj91VRWn96DkukG4bwdtyTh' => 'GENESIS',
    'rhWcbzUj9SVJocfHGLn58VYzXvoVnsU44u' => 'ktbunny',
    'rEUXZtdhEtCDPxJ3MAgLNMQpq4ASgjrV6i' => 'GENESIS',
    'rJFGHvCtpPrftTmeNAs8bYy5xUeTaxCD5t' => 'lebing',
    'rNSnpURu2o7mD9JPjaLsdUw2HEMx5xHzd' => 'FlipPro',
    'rHTxKLzRbniScyQFGMb3NodmxA848W8dKM' => 'jed',
    'rHDcKZgR7JDGQEe9r13UZkryEVPytV6L6F' => 'GENESIS',
    'rNWzcdSkXL28MeKaPwrvR3i7yU6XoqCiZc' => 'GENESIS',
    'rMYBVwiY95QyUnCeuBQA1D47kXA9zuoBui' => 'GENESIS',
    'rUZRZ2b4NyCxjHSQKiYnpBuCWkKwDWTjxw' => 'GENESIS',
    'rfitr7nL7MX85LLKJce7E3ATQjSiyUPDfj' => 'mojarippledenarnica benod',
    'rUf6pynZ8ucVj1jC9bKExQ7mb9sQFooTPK' => 'aluminumcans',
    'r49pCti5xm7WVNceBaiz7vozvE9zUGq8z2' => 'GENESIS',
    'rPgrEG6nMMwAM1VbTumL23dnEX4UmeUHk7' => 'GENESIS',
    'r9cZA1mLK5R5Am25ArfXFmqgNwjZgnfk59' => 'GENESIS',
    'rD1jovjQeEpvaDwn9wKaYokkXXrqo4D23x' => 'shatripple	X.66',
    'r9aRw8p1jHtR9XhDAE22TjtM7PdupNXhkx' => 'ryansinger	X.24',
    'rLqQ62u51KR3TFcewbEbJTQbCuTqsg82EY' => 'GENESIS',
    'rnGTwRTacmqZZBwPB6rh3H1W4GoTZCQtNA' => 'GENESIS',
    'rauPN85FeNYLBpHgJJFH6g9fYUWBmJKKhs' => 'GENESIS',
    'rEMqTpu21XNk62QjTgVXKDig5HUpNnHvij' => 'jimgolian jimbobway',
    'rGLUu9LfpKyZyeTtSRXpU15e2FfrdvtADa' => 'GENESIS',
    'rshceBo6ftSVYo8h5uNPzRWbdqk4W6g9va' => 'X.11',
    'rDsDR1pFaY8Ythr8px4N98bSueixyrKvPx' => 'cryptoart annette786',
    'rnCiWCUZXAHPpEjLY1gCjtbuc9jM1jq8FD' => 'GENESIS',
    'rKdH2TKVGjoJkrE8zQKosL2PCvG2LcPzs5' => 'GENESIS',
    'rDngjhgeQZj9FNtW8adgHvdpMJtSBMymPe' => 'maxcarjuzaa maxcarjuzaa',
    'rJRyob8LPaA3twGEQDPU2gXevWhpSgD8S6' => 'GENESIS',
    'rphasxS8Q5p5TLTpScQCBhh5HfJfPbM2M8' => 'bitstamp.NejcKodric',
    'ramPgJkA1LSLevMg2Yrs1jWbqPTsSbbYHQ' => 'GENESIS',
    'rsjB6kHDBDUw7iB5A1EVDK1WmgmR6yFKpB' => 'GENESIS',
    'rnj8sNUBCw3J6sSstY9QDDoncnijFwH7Cs' => 'crofoot',
    'rB59DESmVnTwXd2SCy1G4ReVkP5UM7ZYcN' => 'GENESIS',
    'r9ssnjg97d86PxMrjVsCAX1xE9qg8czZTu' => 'GENESIS',
    'rEJkrunCP8hpvk4ijxUgEWnxCE6iUiXxc2' => 'GENESIS',
    'rLCAUzFMzKzcyRLa1B4LRqEMsUkYXX1LAs' => 'jonwaller',
    'r4cmKj1gK9EcNggeHMy1eqWakPBicwp69R' => 'GENESIS',
    'rHSTEtAcRZBg1SjcR4KKNQzJKF3y86MNxT' => 'xiangfu	xiangfu',
    'rnNPCm97TBMPprUGbfwqp1VpkfHUqMeUm7' => 'GENESIS',
    'rwZpVacRQHYArgN3NzUfuKEcRDfbdvqGMi' => 'GENESIS',
    'rHrSTVSjMsZKeZMenkpeLgHGvY5svPkRvR' => 'myleshorton X.10millions',
    'rfCXAzsmsnqDvyQj2TxDszTsbVj5cRTXGM' => 'alexwaters',
    'rfpQtAXgPpHNzfnAYykgT6aWa94xvTEYce' => 'GENESIS',
    'rGRGYWLmSvPuhKm4rQV287PpJUgTB1VeD7' => 'GENESIS',
    'rHXS898sKZX6RY3WYPo5hW6UGnpBCnDzfr' => 'asoltys',
    'r4U5AcSVABL6Ym85jB94KYnURnzkRDqh1Y' => 'GENESIS',
    'rHzWtXTBrArrGoLDixQAgcSD2dBisM19fF' => 'JoelKatz',
    'r4DGz8SxHXLaqsA9M2oocXsrty6BMSQvw3' => 'GENESIS',
    'r9hEDb4xBGRfBCcX3E4FirDWQBAYtpxC8K' => 'GENESIS',
    'rEA2XzkTXi6sWRzTVQVyUoSX4yJAzNxucd' => 'GENESIS',
    'r2oU84CFuT4MgmrDejBaoyHNvovpMSPiA' => 'osmosis',
    'rPFPa8AjKofbPiYNtYqSWxYA4A9Eqrf9jG' => 'GENESIS',
    'rp1xKo4CWEzTuT2CmfHnYntKeZSf21KqKq' => 'GENESIS',
    'rGow3MKvbQJvuzPPP4vEoohGmLLZ5jXtcC' => 'GENESIS',
    'rJ51FBSh6hXSUkFdMxwmtcorjx9izrC1yj' => 'GENESIS',
    'rB5TihdPbKgMrkFqrqUC3yLdE8hhv4BdeY' => 'chrislarsen X.77millions',
    'rUvEG9ahtFRcdZHi3nnJeFcJWhwXQoEkbi' => 'ThuyClint22',
    'r3WjZU5LKLmjh8ff1q2RiaPLcUJeSU414x' => 'GENESIS',
    'rEWDpTUVU9fZZtzrywAUE6D6UcFzu6hFdE' => 'X.15',
    'rhdAw3LiEfWWmSrbnZG3udsN7PoWKT56Qo' => 'mazi	mazi',
    'rf8kg7r5Fc8cCszGdD2jeUZt2FrgQd76BS' => 'ahbritto	X.23',
    'rBY8EZDiCNMjjhrC7SCfaGr2PzGWtSntNy' => 'thekurse',
    'rVehB9r1dWghqrzJxY2y8qTiKxMgHFtQh' => 'GENESIS',
    'rDa8TxBdCfokqZyyYEpGMsiKziraLtyPe8' => 'LeTanque LeTanque',
    'r3kmLJN5D28dHuH8vZNUZpMC43pEHpaocV' => 'OpenCoin.1',
    'rwCYkXihZPm7dWuPCXoS3WXap7vbnZ8uzB' => 'X.12',
    'rsQP8f9fLtd58hwjEArJz2evtrKULnCNif' => 'hazek',
    'rMkq9vs7zfJyQSPPkS2JgD8hXpDR5djrTA' => 'GENESIS',
    'rBQQwVbHrkf8TEcW4h4MtE6EUyPQedmtof' => 'GENESIS',
    'r4q1ujKY4hwBpgFNFx43629f2LuViU4LfA' => 'cuanto',
    'rhDfLV1hUCanViHnjJaq3gF1R2mo6PDCSC' => 'GENESIS',
    'rppWupV826yJUFd2zcpRGSjQHnAHXqe7Ny' => 'GENESIS',
    'r43ksW5oFnW7FMjQXDqpYGJfUwmLan9dGo' => 'GENESIS',
    'rDy7Um1PmjPgkyhJzUWo1G8pzcDan9drox' => 'GENESIS',
    'rU5KBPzSyPycRVW1HdgCKjYpU6W9PKQdE8' => 'Calavera',
    'rQsiKrEtzTFZkQjF9MrxzsXHCANZJSd1je' => 'GENESIS',
    'rsRpe4UHx6HB32kJJ3FjB6Q1wUdY2wi3xi' => 'wtfripple3',
    'rwDWD2WoU7npQKKeYd6tyiLkmr7DuyRgsz' => 'GENESIS',
    'rDCJ39V8yW39Ar3Pod7umxnrp24jATE1rt' => 'GENESIS',
    'rf7phSp1ABzXhBvEwgSA7nRzWv2F7K5VM7' => 'andrewsday',
    'rwoE5PxARitChLgu6VrMxWBHN7j11Jt18x' => 'GENESIS',
    'rnxyvrF2mUhK6HubgPxUfWExERAwZXMhVL' => 'X.52',
    'rKMhQik9qdyq8TDCYT92xPPRnFtuq8wvQK' => 'GENESIS',
    'rnziParaNb8nsU4aruQdwYE3j5jUcqjzFm' => 'pigeons DannyM',
    'rMNKtUq5Z5TB5C4MJnwzUZ3YP7qmMGog3y' => 'GENESIS',
    'rEe6VvCzzKU1ib9waLknXvEXywVjjUWFDN' => 'GENESIS',
    'rPcHbQ26o4Xrwb2bu5gLc3gWUsS52yx1pG' => 'RealFat HostFat',
    'rBrspBLnwBRXEeszToxcDUHs4GbWtGrhdE' => 'GENESIS',
    'rLebJGqYffmcTbFwBzWJRiv5fo2ccmmvsB' => 'Tracey	toz',
    'rPWyiv5PXyKWitakbaKne4cnCQppRvDc5B' => 'BensonSamuel',
    'rUzSNPtxrmeSTpnjsvaTuQvF2SQFPFSvLn' => 'GENESIS',
    'r3PDtZSa5LiYp1Ysn1vMuMzB59RzV3W9QH' => 'elilang plummonkey',
    'rMNzmamctjEDqgwyBKbYfEzHbMeSkLQfaS' => 'ZIGGAP',
    'rHWKKygGWPon9WSj4SzTH7vS4ict1QWKo9' => 'GENESIS',
    'rLp9pST1aAndXTeUYFkpLtkmtZVNcMs2Hc' => 'X.9',
    'rBqCdAqw7jLH3EDx1Gkw4gUAbFqF7Gap4c' => 'GENESIS',
    'rGwUWgN5BEg3QGNY3RX2HfYowjUTZdid3E' => 'TTBitRippleWallet TTBit',
    'rNRG8YAUqgsqoE5HSNPHTYqEGoKzMd7DJr' => 'GENESIS',
    'r8TR1AeB1RDQFabM6i8UoFsRF5basqoHJ'  => 'X.Distributor.1',
    'rpWrw1a5rQjZba1VySn2jichsPuB4GVnoC' => 'lizx',
    'rJQx7JpaHUBgk7C56T2MeEAu1JZcxDekgH' => 'GENESIS',
    'rBnmYPdB5ModK8NyDUad1mxuQjHVp6tAbk' => 'storgonaff	paulie_w',
    'rGqM8S5GnGwiEdZ6QRm1GThiTAa89tS86E' => 'GENESIS',
    'rJZCJ2jcohxtTzssBPeTGHLstMNEj5D96n' => 'GENESIS',
    'rJYMACXJd1eejwzZA53VncYmiK2kZSBxyD' => 'X.FortKnox.b',
    'rLBwqTG5ErivwPXGaAGLQzJ2rr7ZTpjMx7' => 'GENESIS',
    'rhuCtPvq6jJeYF1S7aEmAcE5iM8LstSrrP' => 'GENESIS',
    'r4HabKLiKYtCbwnGG3Ev4HqncmXWsCtF9F' => 'miltonquinnine3',
    'rKHD6m92oprEVdi1FwGfTzxbgKt8eQfUYL' => 'arij',
    'rhxbkK9jGqPVLZSWPvCEmmf15xHBfJfCEy' => 'GENESIS',
  ],

  'known_mainnet' => [
    'rpQwfZuqKAWWLLvXmSMWotWKbfrB68THm5' => 'xMagnetic',
  ],

  'genesis_xahau' => [ //genesis_env(XRPL_NET)
    # GENESIS ACCOUNTS (https://xahauexplorer.com/api/v2/genesis) for Xahau
    'rHb9CJAWyB4rj91VRWn96DkukG4bwdtyTh' => 'GENESIS HOOK', //Genesis hook installed here
    
    'rD74dUPRFNfgnY2NzrxxYRXN4BrfGSN6Mv' => 'XRPL-Labs',
    'rN7XCq12KBvBLKad3wWsVUwmb3dNx1fx3e' => 'Titanium',
    
    'ra7MQw7YoMjUw6thxmSGE6jpAEY3LTHxev' => 'Evernode',
    'rfMB6RCNdWSB6TJXYwCEU5HvDC2eArJp8h' => 'Digital Governance', //https://digitalgoverning.eu/
    'r9EzMEYr5zuRasrfVKdry9JWLbw9mBe6Jg' => 'FYEO',
    
    'r4FRPZbLnyuVeGiSi1Ap6uaaPvPXYZh1XN' => 'Xahau Dev Table',
      'r47tpT8LUoymNgRWzfUq2LdkPRfo4UZSkB' => 'Xahau Dev Table - GENESIS member 1',
      'rxah6E9kpp1Zw98MxYFMoWMQ1NHCZSmyx'  => 'Xahau Dev Table - GENESIS member 2',
      'riLD4RiZcmFLijuBkBr1qLa5tXbojgNSN'  => 'Xahau Dev Table - GENESIS member 3',
      'rscan6NzxxSFxEQST8qALrc5v9mpM8fU1j' => 'XRPScan',
      'rtequsfcSxEerbj18TdS6hUd89vTbaWec'  => 'Tequ',
      'r42noEGvAqfwXnDBebeEPfYVswZVe6CKPo' => 'Bithomp',
      'rWiNRBZaEHFptxtkdohBbk36UxoHVwvEa'  => 'XRPLWin',

    'rHsh4MNWJKXN2YGtSf95aEzFYzMqwGiBve' => 'Xahau Exchange Table',
      'rB3egB3cm51DpFENKH2CameyQJf2fmvN72' => 'Xahau Exchange Table - GENESIS member 1',
      'rGshbE2xPc2Jw66iTAXuX5RjXmJW4ohbrY' => 'Xahau Exchange Table - GENESIS member 2',
      'r4vv7gFjtWAUxPWfj5puGNeW9U8FGSn7iu' => 'Xahau Exchange Table - GENESIS member 3',
      'r4FF5jjJMS2XqWDyTYStWrgARsj3FjaJ2J' => 'GateHub UAB',
      'rfKsmLP6sTfVGDvga6rW6XbmSFUzc3G9f3' => 'Bitrue',
    
    'r6QZ6zfK37ZSec5hWiQDtbTxUaU2NWG3F'  => 'Xahau Auditors and Enterprise',
      'rpuQonHVeMk1qEz9bWMhDRBDSjvLu2gU1B' => 'Xahau Auditors and Enterprise - GENESIS member 1',
      'rHrptekd18qAGCADzK1kg2QyREiRPuVpTJ' => 'Xahau Auditors and Enterprise - GENESIS member 2',
      'rpZuvdsDzCLxii1ag9TAyf11Wc43qg4QAG' => 'Xahau Auditors and Enterprise - GENESIS member 3',
      'rwcL4h6ix5VrxjE6GXNq2svJjnj6H3ZJjv' => 'Xahau Auditors and Enterprise - GENESIS member 4',

    'rwyypATD1dQxDbdQjMvrqnsHr2cQw5rjMh' => 'Xahau Projects Table',
      'rHMtqVuvEESUhPrsgb8tSa5ghjyoQySfVC' => 'xSPECTAR',
      'rJFxdrd1BuMeJshRAZBuHP3hex9DjH1nnr' => 'Eminence',
  ],

  'known_xahau' => [
    'rEvernodee8dJLaFsujS6q1EiXvZYmHXr8' => 'Evernode Issuer',
    'rBvKgF3jSZWdJcwSsmoJspoXLLDVLDp6jg' => 'Evernode Governor',
    'rHktfGUbjqzU4GsYCMc1pDjdHXb5CJamto' => 'Evernode Heartbeat',
    'rmv53yu8Wid6kj6AC6NvmiwSXNxRa8vTH' => 'Evernode Registry',
    'rhMboq72S1sLBBxv4PS6ezwJLbgfSJFG4b' => 'Evernode Claimer',
    'rPNFrWbZG7mPenXAEBjAkPezE5N6NKy4W' => 'Evernode Labs',

    'rE7EjcVNHjE6JdpCoDdCXjfMoKExNiKkKi' => 'Xahau Bet',

    'rpQwfZuqKAWWLLvXmSMWotWKbfrB68THm5' => 'xMagnetic',
    'rDEMHZSisVkChmGZzqvrmqRBYnbiV1dZ5f' => 'Meliora ByteLabs',
    'r3qZMoq4JgbHsGKbbDHof6Pi1Y9gkstM22' => 'KrazeDegen',
  ]

];