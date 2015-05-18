<?php

// OAuthのいつもの
$consumer_key = '2USAWJZyyfGxvQhH6E6hc4eFS';
$consumer_secret = 'hrtT4xkedxPiyju7vV8GXEMfIxSTQwSyxccfqEKYgH439qmll8';
$oauth_token = '1106631758-YgoQdr2rpOZfRjZmFxugudOVytjKtvtAAybVKz6';
$oauth_token_secret = 'XpXzHaLUJ2vWBXVmm6QXJCVBgxDN3wK3GcfEywJO9y5a8';

// APIのURL
$url = 'https://stream.twitter.com/1.1/statuses/filter.json';

// リクエストのメソッド
$method = 'GET';

// パラメータ
$post_parameters = array(
);
$get_parameters = array(
    'locations' => '132.2,29.9,146.2,39.0,138.4,33.5,146.1,46.20',
);
$oauth_parameters = array(
    'oauth_consumer_key' => $consumer_key,
    'oauth_nonce' => microtime(),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => time(),
    'oauth_token' => $oauth_token,
    'oauth_version' => '1.0',
);

// 署名を作る
$a = array_merge($oauth_parameters, $post_parameters, $get_parameters);
ksort($a);
$base_string = implode('&', array(
    rawurlencode($method),
    rawurlencode($url),
    rawurlencode(http_build_query($a, '', '&', PHP_QUERY_RFC3986))
));
$key = implode('&', array(rawurlencode($consumer_secret), rawurlencode($oauth_token_secret)));
$oauth_parameters['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_string, $key, true));


// 接続＆データ取得
// $fp = stream_socket_client("ssl://stream.twitter.com:443/"); でもよい
$fp = fsockopen("ssl://stream.twitter.com", 443);
if ($fp) {
    fwrite($fp, "GET " . $url . ($get_parameters ? '?' . http_build_query($get_parameters) : '') . " HTTP/1.0\r\n"
        . "Host: stream.twitter.com\r\n"
        . 'Authorization: OAuth ' . http_build_query($oauth_parameters, '', ',', PHP_QUERY_RFC3986) . "\r\n"
        . "\r\n");
    while (!feof($fp)) {
        var_dump(fgets($fp));
    }
    fclose($fp);
}
