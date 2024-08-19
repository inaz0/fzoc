<?php

require_once('config.php');

//-- on va parcourir notre dossier pour réaliser les compilations
$pending_task = scandir( $task_list );

unset( $pending_task[0], $pending_task[1]);

foreach( $pending_task ?? [] as $task_waiting ){

    if( !is_dir( $task_list.$task_waiting ) ){

        if( rename( $task_list.$task_waiting, $task_list.'running/'.$task_waiting ) ) {

            ob_start();
            passthru( 'bash '.$task_list.'running/'.$task_waiting  );
            $var = ob_get_contents();
            ob_end_clean(); 
        }
        else{

            echo 'error move';
        }
    }  
}