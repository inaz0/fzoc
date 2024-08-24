<?php

require_once('config.php');


$sql_update_compiled_req = ' UPDATE fzco_compiled SET compiled_status="deleted" WHERE compiled_firmware_version_id=:firm_version AND compiled_application_id=:app_id AND compiled_date=:comp_date ';

$sql_compiled_to_delete_req = ' SELECT * FROM fzco_compiled WHERE  DATEDIFF(NOW(),compiled_date) > 4 AND compiled_status != "deleted"';
$sql_compiled_to_delete_req = $bdd_connexion->prepare( $sql_compiled_to_delete_req );
$sql_compiled_to_delete_req->execute();

$sql_compiled_to_delete_res = $sql_compiled_to_delete_req->fetchAll();

if( is_array( $sql_compiled_to_delete_res ) && count( $sql_compiled_to_delete_res ) > 0 ){

    foreach( $sql_compiled_to_delete_res ?? [] as $a_compil_to_delete ){

        try{

            unlink($fap_path.$a_compil_to_delete[ 'compiled_path_fap' ]);

            $sql_update_compiled_req = $bdd_connexion->prepare( $sql_update_compiled_req );
            $sql_update_compiled_req->execute( [ 
                'firm_version' => $a_compil_to_delete[ 'compiled_firmware_version_id' ],
                'app_id'       => $a_compil_to_delete[ 'compiled_application_id' ],
                'comp_date'    => $a_compil_to_delete[ 'compiled_date' ]
                 ] );
        }
        catch(Exception $e){

            if( $debug === true ){

                echo $e->getMessage();
            }

            echo 'deleting error';
        }
    }
}