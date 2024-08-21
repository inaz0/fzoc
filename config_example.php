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
$task_list = __DIR__.'/tasks/';

//-- path to fap
$fap_path = __DIR__.'/public/faps/';

require_once( 'class/fzcoPDO.class.php' );

?>