<?php

require_once('./vendor/autoload.php');
require_once('./keys.php');
require_once('./classes/TwitterManager.php');

$tm = new TwitterManager($config_tokens[ACCOUNT_TREND]);

# $list_id = $tm->list_name_to_id("students");
$list_id = 86036548;
$count = 1000;
$member_ids = $tm->get_list_member_ids($list_id, $count);

$mention_ids = $tm->get_statuses_mentions_user_ids();
var_dump($mention_ids);
exit();
$apply_ids = array_diff($mention_ids, $member_ids);
var_dump($apply_ids);
exit();
$tm->post_lists_members_create_all($list_id, $apply_ids);

/*
* lists/members/create_all
 */
