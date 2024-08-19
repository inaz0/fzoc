<?php

require_once('config.php');

//-- on va parcourir notre dossier pour réaliser les compilations
$pending_task = scandir( $task_list );

unset( $pending_task[0], $pending_task[1]);

foreach( $pending_task ?? [] as $task_waiting ){

    $task_waiting = $task_list.'/'.$task_waiting;

    rename( $task_waiting, 'running/'.$task_waiting.'.sh' );
    chmod('running/'.$task_waiting.'.sh', 755);
    shell_exec( './running/'.$task_waiting.'.sh' );
}