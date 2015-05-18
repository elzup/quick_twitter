<?php

require_once('./vendor/autoload.php');
require_once('./keys.php');
require_once('./classes/TwitterManager.php');

$tm = new TwitterManager($config_tokens[ACCOUNT_TREND]);

# $list_id = $tm->list_name_to_id("students");
$list_id = 86036548;
$count = 1000;
$member_ids = $tm->get_list_member_ids($list_id, $count);

var_dump($member_ids);
