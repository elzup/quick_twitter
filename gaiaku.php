<?php

set_time_limit(100);
require_once('./vendor/autoload.php');

$consumer_key = '8hnRAJJaXdy1SnNtN8cRnQ53t';
$consumer_secret = 'yGufamVg2BY2zvUej2rTt84Hk3M7LQsUSVWZKNcWf51hhj69bv';
$oauth_token = '2921834160-qbfC0O6gFuIQJrU2QIdIu7cn73kNQKdhVDGcqI2';
$oauth_token_secret = 'SCsCCKSwHwO2Q2kz1JOEbqSCiXSOKcITCyAczkJzQcWnJ';
 
$to = new TwistOAuth($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret);

$query = 'search/tweets';
$params = array(
    'q' => '髪切りたい',
    'count' => 100,
    'result_type' => 'recent',
);
$sts = $to->get($query, $params);
foreach ($sts->statuses as $st) {
    $query = 'statuses/update';
    $text = '@' . $st->user->screen_name . ' お前のどこに切る髪あるんだよ.';
    $params = array(
        'status' => $text,
        'in_reply_to_status_id' => $st->id,
    );
    $to->post($query, $params);
}
