<?php

require_once('config.php');

//-- on va parcourir notre dossier pour réaliser les compilations
$pending_task = scandir( $task_list );

unset( $pending_task[0], $pending_task[1]);

foreach( $pending_task ?? [] as $task_waiting ){

    if( !is_dir( $task_list.$task_waiting ) ){

        if( rename( $task_list.$task_waiting, $task_list.'running/'.$task_waiting ) ) {

            shell_exec( 'bash '.$task_list.'running/'.$task_waiting .' > '.$task_list.'result/'.str_replace('.sh','',$task_waiting).'.result');

            //-- on récupère le contenu du resutl si on trouve "Found nothing to build" on le met en "build impossible"
            //-- update le status pour chaque tache
            // remove les gits
        }
        else{

            echo 'error move';
        }
    }  
}