<?php

//-- si jamais on a pas le config.php on peut prendre le example notamment pour docker
if( !is_file( __DIR__.'/../config.php' ) ){

    require_once( __DIR__.'/../config_example.php' );
}
else{

    require_once( __DIR__.'/../config.php' );
}

//-- on va parcourir notre dossier pour réaliser les compilations
$pending_task = scandir( $task_list );

unset( $pending_task[0], $pending_task[1]);

$sql_update_compiled_query = 'UPDATE fzco_compiled SET compiled_status=:new_status WHERE compiled_path_fap = :compiled_path';

foreach( $pending_task ?? [] as $task_waiting ){

    if( !is_dir( $task_list.$task_waiting ) ){

        if( rename( $task_list.$task_waiting, $task_list.'running/'.$task_waiting ) ) {

            $result_file = $task_list.'result/'.str_replace('.sh','',$task_waiting).'.result';
            shell_exec( 'sh '.$task_list.'running/'.$task_waiting .' > '.$result_file );

            //-- on récupère le contenu du resutl si on trouve "Found nothing to build" on le met en "build impossible"
            //-- update le status pour chaque tache
            // remove les gits

            $sql_update_compiled = $bdd_connexion->prepare( $sql_update_compiled_query );

            $check_result          = file_get_contents( $result_file );
            $correct_compiled_path = str_replace( ['_', '.sh'], ['/',''] , $task_waiting );

            if( preg_match('/Found nothing to build/iu', $check_result) ){

                $sql_update_compiled->execute( ['new_status' => 'impossible', 'compiled_path' => $correct_compiled_path ] );
            } 
            else{

                $sql_update_compiled->execute( ['new_status' => 'success', 'compiled_path' => $correct_compiled_path ] );
            }

            //-- on supprime le répertoire clonné il n'est plus utile
            shell_exec( 'rm -rf ../gits/'.$correct_compiled_path );

            //-- on retire la tache et le fichier result don nous n'avons plus besoin
            shell_exec( 'rm -rf '.$task_list.'running/'.$task_waiting);
            shell_exec( 'rm -rf '.$result_file);
        }
        else{

            echo 'error move';
        }
    }  
}