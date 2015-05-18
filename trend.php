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
$apply_ids = array_diff($mention_ids, $member_ids);
var_dump($apply_ids);

# $id_str = "3140585499,160914008,2930261450,1108075561,2497162742,817068877,1547840870,3134482459,228715315,2692723548,3143106979,2162373878,119977470,216304918,3093603883,1366065500,3018372642,173339655,711980702,1443007674,808652600,1356992954,279546560,1189093320,3156340692,156313964,3024242298,2921837443,1422390272,3099492620,567106924,2501370445,3130948602,2932025329,580716127,83323770,2665185854,3039555145,586600424,83547038,526467895,1320527354,1145493398,977612221,190007570,960099973,1850592775,636802395,232174363,2153172372";
# $ids = explode(",", $id_str);
$tm->post_lists_members_create_all($list_id, $apply_ids);

/*
* lists/members/create_all
 */
