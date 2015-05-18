<?php

require_once('./vendor/autoload.php');
require_once('./keys.php');
require_once('./classes/TwitterManager.php');

$tm = new TwitterManager($config_tokens[ACCOUNT_TREND]);

$tm->
