<?php

define('ACCOUNT_ARZZUP', 0);
define('ACCOUNT_TEST', 1);
define('ACCOUNT_DENPA', 2);
define('ACCOUNT_ELMANE', 3);
define('ACCOUNT', ACCOUNT_ARZZUP);
require_once('./vendor/autoload.php');
require_once('./keys.php');

$to = new TwistOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);
$case = 3;
//$case = 3;
switch ($case) {
case 1:
    $query = 'statuses/update';
    $text = <<<EOF
　　今年もよろしく
　　　　お願いします

EOF;
    $params = array(
        'status' => $text,
    );
    $res = $to->post($query, $params);
    var_dump($res);
    break;
case 2:
    $query = 'statuses/mentions_timeline';
    $params = array(
        'count' => 200,
    );
    $res = $to->get($query, $params);
    $list = array();
    foreach ($res as $st) {
        $list[] = $st->user->screen_name . PHP_EOL;
    }
    foreach (array_unique($list) as $sn) {
        echo trim($sn) . PHP_EOL;
    }
    break;
case 3:
    $query = 'statuses/update';
    $sns = explode(' ', 'gen_nnn toritoriiiiiii s_dm_u pear510 uzuyh aiobo YlunaticY2 nanase_coder seiun_net nekogayome wowata_c kimura0726 namanamaallegg alter095 nomuken Coppertdu hikaru__m Spanish_tickler ryu511_tdu mikekuroe akameco riho_0906 keisei_1092 kalabu_umes Keyaki_4521 reirin88 twinkfrag washa_sha mikekuroe NavyBlooming sukonbu0909 anitem1 godslew 18ckforeverlove noppy17m munisystem');
    foreach ($sns as $sn) {
        $text = <<<EOF
@{$sn}
🍀🍀🍀|🍀🍀🍀|🍀|🍀🍀🍀
🐑🗻🍀|🍀🍣🍀|🍀|🍀🐑🐑
🍀🍀🍀|🍀🐑🍀|🍀|🍀🍀🍀
🍀🐑🐑|🍀🐑🍀|🍀|✌🐑🍀
🍀🍀🍀|🍀🍀🍀|🍀|🍀🍀🍀
　今年も一年よろしく
　　お願いします
　　　ざっぷ
EOF;
        $params = array(
            'status' => $text,
        );
        $res = $to->post($query, $params);
    }
    break;
case 4:
    $query = 'friendships/lookup';
    $sns = 'gen_nnn,toritoriiiiiii,s_dm_u,pear510,uzuyh,aiobo,YlunaticY2,nanase_coder,seiun_net,nekogayome,wowata_c,kimura0726,namanamaallegg,alter095,nomuken,Coppertdu,hikaru__m,Spanish_tickler,ryu511_tdu,mikekuroe,akameco,riho_0906,keisei_1092,kalabu_umes,Keyaki_4521,twinkfrag,washa_sha,mikekuroe,NavyBlooming,sukonbu0909,anitem1,godslew,exnn,kawakatsuM';
    $params = array(
        'screen_name' => $sns,
    );
    $res = $to->get($query, $params);
    foreach ($res as $user) {
        echo sprintf("%20s %s:%s\n", $user->screen_name, (in_array('following', $user->connections) ? "o":"x"), (in_array('followed_by', $user->connections) ? "o":"x"));
    }
    break;
case 5:
    $query = 'application/rate_limit_status';
    $res = $to->get($query);
    foreach ($res->resources as $cate) {
        foreach ($cate as $query => $v) {
            echo "{$query} ($v->remaining} / {$v->limit})\n";
        }
    }
    var_dump($res);
    break;
default:
    break;
}

