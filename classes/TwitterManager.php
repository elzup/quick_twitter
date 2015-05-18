<?php

class TwitterManager {

    public $to;

    public function __construct($cfg) {
        $this->to = new TwistOAuth($cfg['CONSUMER_KEY'], $cfg['CONSUMER_SECRET'], $cfg['OAUTH_TOKEN'], $cfg['OAUTH_TOKEN_SECRET']);
    }

    public function get_home_timeline($count = 200) {
        return $this->get_timeline($count);
    }

    public function get_timeline($count = 100, $screen_name = NULL) {
        $query = 'statuses/home_timeline';
        $params = array();
        $params['count'] = $count;
        if (isset($screen_name)) {
            $query = 'statuses/user_timeline';
            $params['screen_name'] = $screen_name;
        }
        $res = $this->to->get($query, $params);
        if (isset($res['errors'])) {
            echo $res['errors'];
        }
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
        $params = array(
            'count' => 5000,
        );
        $res = $this->to->get('friends/ids', $params);
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

    public function post_tweet($text) {
        $query = 'statuses/update';
        $params = array(
            'status' => $text,
        );
        $res = $this->to->post($query, $params);
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

    public function get_list_lists() {
        $query = 'lists/list';
        return $this->to->get($query);
    }

    public function get_list_members($list_id, $count = 20) {
        $query = 'lists/members';
        $params = array(
            'list_id' => $list_id,
            'count' => $count,
        );
        return $this->to->get($query, $params);
    }

    public function get_list_member_ids($list_id, $count = 20) {
        $members = $this->get_list_members($list_id, $count);
        $ids = array();
        foreach ($members->users as $member) {
            $ids[] = $member->id;
        }
        return $ids;
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

    public function list_name_to_id($name) {
        foreach ($this->get_list_lists() as $list) {
            if ($list->name == $name) {
                return $list->id;
            }
        }
        return NULL;
    }
}
