<?php

define('ACCOUNT_ARZZUP', 0);
define('ACCOUNT_TEST', 1);
define('ACCOUNT', ACCOUNT_ARZZUP);
require_once('./vendor/autoload.php');
require_once('./keys.php');

$url = 'https://userstream.twitter.com/1.1/user.json';
$method = 'GET';

// パラメータ
$oauth_parameters = array(
    'oauth_consumer_key' => CONSUMER_KEY,
    'oauth_nonce' => microtime(),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_token' => OAUTH_TOKEN,
    'oauth_version' => '1.0',
);
// 署名を作る
$a = $oauth_parameters;
ksort($a);
$base_string = implode('&', array(
    rawurlencode($method),
    rawurlencode($url),
    rawurlencode(http_build_query($a, '', '&', PHP_QUERY_RFC3986))
));
$key = implode('&', array(rawurlencode(CONSUMER_SECRET), rawurlencode(OAUTH_TOKEN_SECRET)));
$oauth_parameters['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_string, $key, true));

$port = 5001;
$url = 'tcp://localhost:' . $port;
$timeout = 30;

$fp_tcp = fsockopen($url, $port, $errno, $errstr, $timeout);
if (!$fp_tcp || $errno > 0) {
    print( "$errno ($errstr) \n" );
    exit();
}

// 接続＆データ取得
$fp = fsockopen("ssl://userstream.twitter.com", 443);
if ($fp) {
    fwrite($fp, "GET " . $url . " HTTP/1.1\r\n"
        . "Host: userstream.twitter.com\r\n"
        . 'Authorization: OAuth ' . http_build_query($oauth_parameters, '', ',', PHP_QUERY_RFC3986) . "\r\n"
        . "\r\n");
    while (!feof($fp)) {
        $res = fgets($fp);
        $res = json_decode($res, true);
        if (!isset($res['id'])) {
            continue;
        }
        //表示
        $msg = "[{$res['id']}]: {$res['text']}";
        $send_text = sprintf( "%s %s\n", date( 'YmdHis' ), $input );

        print( 'SEND>>' . $send_text );
        fwrite( $fp_tcp, $send_text );
        print( 'RECV<<' . fgets( $fp_tcp, 4096 ) );

        if( $input == 'exit' ) {
            break;
        }
        echo 'posted: ' . $msg;
    }
    fclose($fp);
    fclose($fp_tcp);
}

/*

Array
(
    [created_at] => Mon Nov 17 18:44:03 +0000 2014
    [id] => 534416692953104384
    [id_str] => 534416692953104384
    [text] => 流石に寝ないと明日に響く
    [source] => <a href="http://twitter.com/download/iphone" rel="nofollow">Twitter for iPhone</a>
    [truncated] => 
    [in_reply_to_status_id] => 
    [in_reply_to_status_id_str] => 
    [in_reply_to_user_id] => 
    [in_reply_to_user_id_str] => 
    [in_reply_to_screen_name] => 
    [user] => Array
        (
            [id] => 793580964
            [id_str] => 793580964
            [name] => 河の字
            [screen_name] => Tkawanoji
            [location] => 
            [profile_location] => 
            [url] => 
            [description] => 
            [protected] => 1
            [followers_count] => 55
            [friends_count] => 63
            [listed_count] => 0
            [created_at] => Fri Aug 31 10:39:30 +0000 2012
            [favourites_count] => 145
            [utc_offset] => 32400
            [time_zone] => Tokyo
            [geo_enabled] => 
            [verified] => 
            [statuses_count] => 2126
            [lang] => ja
            [contributors_enabled] => 
            [is_translator] => 
            [is_translation_enabled] => 
            [profile_background_color] => C0DEED
            [profile_background_image_url] => http://abs.twimg.com/images/themes/theme1/bg.png
            [profile_background_image_url_https] => https://abs.twimg.com/images/themes/theme1/bg.png
            [profile_background_tile] => 
            [profile_image_url] => http://pbs.twimg.com/profile_images/2561926071/image_normal.jpg
            [profile_image_url_https] => https://pbs.twimg.com/profile_images/2561926071/image_normal.jpg
            [profile_link_color] => 0084B4
            [profile_sidebar_border_color] => C0DEED
            [profile_sidebar_fill_color] => DDEEF6
            [profile_text_color] => 333333
            [profile_use_background_image] => 1
            [default_profile] => 1
            [default_profile_image] => 
            [following] => 
            [follow_request_sent] => 
            [notifications] => 
        )

    [geo] => 
    [coordinates] => 
    [place] => 
    [contributors] => 
    [retweet_count] => 0
    [favorite_count] => 0
    [entities] => Array
        (
            [hashtags] => Array
                (
                )

            [symbols] => Array
                (
                )

            [user_mentions] => Array
                (
                )

            [urls] => Array
                (
                )

        )

    [favorited] => 
    [retweeted] => 
    [filter_level] => medium
    [lang] => ja
    [timestamp_ms] => 1416249843239
)
*/
