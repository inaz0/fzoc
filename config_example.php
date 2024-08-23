<?php

//-- Database configuration
$bdd_username = 'fzco';
$bdd_password = 'xxx';
$bdd_name     = 'fzco';
$bdd_host     = '127.0.0.1';
$bdd_port     = 3306;

$is_maintenace_mode = false;

//-- put to false for production
$debug              = false;

//-- ufbt configuration
$path_to_ufbt = __DIR__.'/ufbt/';

//-- task list
$task_list         = __DIR__.'/tasks/';
$path_task_updates = __DIR__.'/tasks_update/'; 

//-- path to fap
$fap_path = __DIR__.'/public/faps/';

//-- cloudflare
$is_active_cloudflaire_turnstile = true;
$cloudflare_turnstile_sitekey    = 'fake_data';
$cloudflare_turnstile_secretkey  = 'fake_data';

require_once( 'class/fzcoPDO.class.php' );

?>