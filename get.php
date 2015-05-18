<?php

define('ACCOUNT_ARZZUP', 0);
define('ACCOUNT_TEST', 1);
define('ACCOUNT', ACCOUNT_ARZZUP);
require_once('./vendor/autoload.php');
require_once('./keys.php');

$to = new TwistOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

get_tweets('');
exit();
#艦これ版深夜の真剣お絵描き60分一本勝負
$statuses = search_tweets("#艦これ版深夜の真剣お絵描き60分一本勝負");
foreach ($statuses->statuses as $st) {
    echo "\n-----\n";
    echo $st->user->name . PHP_EOL;
    echo $st->text . PHP_EOL;
    echo $st->source . PHP_EOL;
}

# $times = array();
# foreach(array_reverse(get_tweets('hnle0')) as $st) {
#     echo date('H:i:s', strtotime($st->created_at));
#     if (isset($st->entities->media)) {
#         echo '■';
#     }
#     @$times[date('Hi', strtotime($st->created_at))][] = isset($st->entities->media);
#     echo PHP_EOL;
# }
# 
# foreach ($times as $k => $t) {
#     echo $k . ': ';
#     foreach ($t as $i) {
#         echo $i ? "o" : "-";
#     }
#     echo PHP_EOL;
# }
// ,.()<>!@#$%^&*:;'"
// ,.()  !@#$%^&*:;'  <>"
//
// ~`/?[]\|+_-=
// ~`/?[]\|+_-=

//$hashes = get_friends_profile_image_hashes();
//echo implode("\n", $hashes);

function get_tweets($screen_name = NULL) {
    global $to;
    $param = array(
        'screen_name' => $screen_name,
        'count' => 200,
    );
    $res = $to->get('statuses/user_timeline', $param);
    if (isset($res->errors)) {
        var_dump($res->errors);
        return;
    }
    return $res;
}
function get_friends_profile_image_hashes($screen_name = NULL) {
    $hashes = array();
    foreach (get_friends_profile_image($screen_name) as $url) {
        if (preg_match('#profile_images/(?|default_profile_([0-9])|(\d+/.*))$#', $url, $m)) {
            $hashes[] = $m[1];
        } else {
            echo 'no-hits -> ' . $url . PHP_EOL;
        }
    }
    return $hashes;
}

function get_friends_profile_image($screen_name = NULL) {
    global $to;
    $param = array(
        'count' => 5000,
    );
    $res = $to->get('friends/ids', $param);
    if (isset($res->errors)) {
        var_dump($res->errors);
        return;
    }
    $urls = array();
    foreach (array_chunk($res->ids, 100) as $ids) {
        $ids_str = implode(',', $ids);
        $res = $to->get('users/lookup', array(
            'user_id' => $ids_str,
        ));
        if (isset($res->errors)) {
            var_dump($res->errors);
            return;
        }
        foreach ($res as $user) {
            $urls[] = $user->profile_image_url;
        }
    }
    return $urls;
}

function get_timeline($count = 100, $screen_name = NULL) {
    global $to;
    $query = 'statuses/home_timeline';
    $param = array(
        'count' => $count,
    );
    if (isset($screen_name)) {
        $query = 'statuses/user_timeline';
        $param['screen_name'] = $screen_name;
    }
    $res = $to->get($query, $param);
    if (isset($res->errors)) {
        echo $res->errors;
    }
    return $timeline;
}

function search_tweets($q = "#nowplaying μ's") {
    global $to;
    $source_list = array();
    $query = 'search/tweets';
    $params = array(
        'q' => $q,
        'count' => 200,
    );
    return $to->get($query, $params);
}

function get_nowplaying($q = "#nowplaying μ's") {
    global $to;
    $source_list = array();
    $query = 'search/tweets';
    $params = array(
        'q' => $q,
        'count' => 100,
    );
    $res = $to->get($query, $params);
    foreach ($res->statuses as $st) {
        $t = '<a href="http://www.jisakuroom.net/" rel="nofollow">なうぷれTunes</a>';
        if (preg_match('#<a.*href="(?<link>.*?)".*?>(?<source>.*)</a>#u', $st->source, $m)) {
            echo $m['source'] . '[' . $m['link'] . ']';
            echo PHP_EOL . '----' . PHP_EOL;
            $source_list[] = $m['source'];
        }
        echo $st->text;
        echo PHP_EOL . '----' . PHP_EOL;
    }
    return array_values(array_unique($source_list));
}

function sn_to_id($sn) {
    global $to;
    // screen_name to id
    $query = 'users/lookup';
    $params = array(
        'screen_name' => 'akameco',
    );
    $res = $to->get($query, $params);
    return $res->id;
}

function change_user_name($name) {
    global $to;
    // screen_name to id
    $query = 'account/update_profile';
    $params = array(
        'name' => $name,
    );
    $res = $to->post($query, $params);
    return $res;
}

function get_user_tweet($sn) {
    global $to;
    // screen_name to id
    $query = 'statuses/user_timeline';
    $params = array(
        'screen_name' => $sn,
    );
    $res = $to->get($query, $params);
    return $res;
}
