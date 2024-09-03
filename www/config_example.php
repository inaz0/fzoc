<?php

//-- Database configuration
$bdd_username = getenv('BDD_USERNAME', true) ? getenv('BDD_USERNAME') : 'fzco';
$bdd_password = getenv('BDD_PASSWORD', true) ?  getenv('BDD_PASSWORD') : 'fzco' ;
$bdd_name     = getenv('BDD_NAME', true) ? getenv('BDD_NAME') : 'fzco' ;
$bdd_host     = 'db_fzoc';
$bdd_port     = 3306;

//-- put to false for production
$debug              = getenv('DEBUG', true) ? getenv('DEBUG') : true;

//-- ufbt configuration
$path_to_ufbt = getenv('UFBT_PATH', true) ?  getenv('UFBT_PATH') : __DIR__.'/ufbt/';


//-- task list
$task_list         = __DIR__.'/tasks/';
$path_task_updates = __DIR__.'/tasks_update/'; 

//-- path to fap
$fap_path = __DIR__.'/public/faps/';

//-- cloudflare
$is_active_cloudflare_turnstile = getenv('IS_ACTIVE_CLOUDFLARE_TURNSTILE', true) ?  getenv('IS_ACTIVE_CLOUDFLARE_TURNSTILE') : false;
$cloudflare_turnstile_sitekey   = getenv('CLOUDFLARE_TURNSTILE_SITEKEY', true) ?  getenv('CLOUDFLARE_TURNSTILE_SITEKEY') : 'fake';
$cloudflare_turnstile_secretkey = getenv('CLOUDFLARE_TURNSTILE_SERVERKEY', true) ?  getenv('CLOUDFLARE_TURNSTILE_SERVERKEY') : 'fake';

require_once( 'class/fzcoPDO.class.php' );


?>