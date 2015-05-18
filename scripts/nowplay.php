<?php

define('ACCOUNT_ARZZUP', 0);
define('ACCOUNT_TEST', 1);
define('ACCOUNT_ELMANE', 2);
define('ACCOUNT_DENPA', 3);
define('ACCOUNT', ACCOUNT_DENPA);
require_once('./vendor/autoload.php');
require_once('./keys.php');

$to = new TwistOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

for ($i = 0; $i < 1; $i ++) {
    $query = 'search/tweets';
    $params = array(
        'q' => "七面鳥 since:2014-12-24 until:2014-12-25",
        'count' => '10',
    );
    $res = $to->get($query, $params);

    var_dump($res->statuses);
    foreach ($res->statuses as $st) {
        echo str_replace("\n", ' ', $st->text) . PHP_EOL;
    }
}

