<?php

require_once('config.php');

//-- on va parcourir notre dossier pour réaliser les compilations
$pending_task = scandir( $task_list );

unset( $pending_task[0], $pending_task[1]);

foreach( $pending_task ?? [] as $task_waiting ){

    if( !is_dir( $task_list.$task_waiting ) ){

        if( rename( $task_list.$task_waiting, $task_list.'running/'.$task_waiting ) ) {

            echo shell_exec( 'bash '.$task_list.'running/'.$task_waiting );
        }
        else{

            echo 'error move';
        }
    }  
}