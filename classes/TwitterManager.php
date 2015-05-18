<?php

class TwitterManager {

    public $to;

    public function __construct($cfg) {
        $this->to = new TwistOAuth($cfg['CONSUMER_KEY'], $cfg['CONSUMER_SECRET'], $cfg['OAUTH_TOKEN'], $cfg['OAUTH_TOKEN_SECRET']);
    }

    public function get_tweets($screen_name = NULL) {
        $param = array(
            'screen_name' => $screen_name,
            'count' => 200,
        );
        $res = $this->to->get('statuses/user_timeline', $param);
        if (isset($res->errors)) {
            var_dump($res->errors);
            return;
        }
        return $res;
    }
    public function get_friends_profile_image_hashes($screen_name = NULL) {
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

    public function get_friends_profile_image($screen_name = NULL) {
        $param = array(
            'count' => 5000,
        );
        $res = $this->to->get('friends/ids', $param);
        if (isset($res->errors)) {
            var_dump($res->errors);
            return;
        }
        $urls = array();
        foreach (array_chunk($res->ids, 100) as $ids) {
            $ids_str = implode(',', $ids);
            $res = $this->to->get('users/lookup', array(
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

    public function get_timeline($count = 100, $screen_name = NULL) {
        $query = 'statuses/home_timeline';
        $param = array(
            'count' => $count,
        );
        if (isset($screen_name)) {
            $query = 'statuses/user_timeline';
            $param['screen_name'] = $screen_name;
        }
        $res = $this->to->get($query, $param);
        if (isset($res['errors'])) {
            echo $res['errors'];
        }
        return $res;
    }

    public function search_tweets($q = "#nowplaying μ's") {
        $source_list = array();
        $query = 'search/tweets';
        $params = array(
            'q' => $q,
            'count' => 200,
        );
        return $this->to->get($query, $params);
    }

    public function get_nowplaying($q = "#nowplaying μ's") {
        $source_list = array();
        $query = 'search/tweets';
        $params = array(
            'q' => $q,
            'count' => 100,
        );
        $res = $this->to->get($query, $params);
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

    public function sn_to_id($sn) {
        // screen_name to id
        $query = 'users/lookup';
        $params = array(
            'screen_name' => 'akameco',
        );
        $res = $this->to->get($query, $params);
        return $res->id;
    }

    public function change_user_name($name) {
        // screen_name to id
        $query = 'account/update_profile';
        $params = array(
            'name' => $name,
        );
        $res = $this->to->post($query, $params);
        return $res;
    }

    public function get_user_tweet($sn) {
        // screen_name to id
        $query = 'statuses/user_timeline';
        $params = array(
            'screen_name' => $sn,
        );
        $res = $this->to->get($query, $params);
        return $res;
    }
}
