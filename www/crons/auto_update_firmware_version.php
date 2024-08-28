<?php
//-- si jamais on a pas le config.php on peut prendre le example notamment pour docker
if( !is_file( '../config.php' ) ){

    require_once( '../config_example.php' );
}
else{

    require_once( '../config.php' );
}



//-- on va récupérer les firmwares
$all_firmware_req = $bdd_connexion->prepare('
    SELECT 

        * 
    
    FROM fzco_firmware 
    
    INNER JOIN fzco_depend ON depend_firmware_id = firmware_id
    INNER JOIN fzco_firmware_version ON depend_firmware_version_id = firmware_version_id

    WHERE firmware_is_active=1 AND firmware_version_is_active=1 AND firmware_version_type = "release"
    
    ');

$all_firmware_req->execute();
$all_firmware_res = $all_firmware_req->fetchAll();

foreach( $all_firmware_res as $value_firm ){

    
    $curl = curl_init();

    // fixe l'URL et les autres options appropriées
    $options_curl = array(
        CURLOPT_URL            => $value_firm[ 'firmware_url_update' ],
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS      => 0,        
    );

    curl_setopt_array( $curl, $options_curl );

    //-- récupération du contenu
    $response_curl      = curl_exec($curl);
    $response_code_curl = curl_getinfo( $curl, CURLINFO_RESPONSE_CODE );   

    if( $response_code_curl === 200 ){

        $json_directory_data = json_decode( $response_curl );

        if( json_last_error() === JSON_ERROR_NONE ){

            $task_file_for_udpate = $path_task_updates.'udapte_needed_'.time().'.sh';
	
            if( property_exists($json_directory_data, 'channels') ){ 
                
                foreach( $json_directory_data->channels as $value_json ){
                    
                    if( property_exists($value_json, 'id') ){ 
                
                        if ( $value_json->id === 'release' ){
                            
                            if( property_exists($value_json, 'versions') ){
                                
                                if( is_array( $value_json->versions ) && count($value_json->versions) >0 && property_exists($value_json->versions[0], 'version') &&  property_exists($value_json->versions[0], 'timestamp') ){


                                    //-- check de la version courante si différente alors on désactive l'ancienne on insert la nouvelle en active + update ufbt
                                    if( $value_firm[ 'firmware_version_name' ] !== $value_json->versions[0]->version ){
                                    
                                        $bdd_connexion->beginTransaction();
                                    
                                        try{
                                            
                                            //-- désactive l'ancienne avant
                                            $sql_deactivate_old_release_req = $bdd_connexion->prepare('
                                            UPDATE fzco_firmware_version SET firmware_version_is_active=0 WHERE firmware_version_id=:firm_version_id
                                            ');
                                        
                                            $sql_deactivate_old_release_req->execute( [ 'firm_version_id' => $value_firm[ 'firmware_version_id' ] ] );
                                        
                                            $sql_create_new_version_firmware_req = $bdd_connexion->prepare( '
                                            INSERT INTO fzco_firmware_version (firmware_version_update_date, firmware_version_type, firmware_version_name, firmware_version_is_active) VALUES (:update_date,"release",:firm_name,1);
                                            ' );
                                            
                                            $sql_create_new_version_firmware_req->execute( [ 'update_date' => date('Y-m-d H:i:s', $value_json->versions[0]->timestamp), 'firm_name' => $value_json->versions[0]->version ]  );
                                            
                                            $sql_create_depend_stm = $bdd_connexion->prepare( 'INSERT INTO fzco_depend (depend_firmware_id, depend_firmware_version_id) VALUES (:firm_id, :firm_version_id);
                                            ' );
                                            $sql_create_depend_stm->execute( [ 'firm_id' => $value_firm['firmware_id'], 'firm_version_id' => $bdd_connexion->lastInsertId() ] );

                                            $bdd_connexion->commit();
                                        }
                                        catch(PDOException $e){
                                            
                                            if( $debug === true ){
                                                
                                                echo $e->getMessage();
                                            }
                                            
                                            $bdd_connexion->rollBack();
                                        }
                                        $state_dir_of_ufbt = $path_to_ufbt.'/fz_'. $value_firm[ 'firmware_ufbt_path' ].'_release';
                                        //-- lancer les update ufbt via un task runner dédié idem que pour les compils
                                        file_put_contents($task_file_for_udpate, 'cd '.$path_to_ufbt.' && . bin/activate && rm -f .env && ufbt dotenv_create --state-dir '.$state_dir_of_ufbt.' && ufbt update --index-url='.$value_firm['firmware_url_update' ].' && rm .env && deactivate'.PHP_EOL ,FILE_APPEND);
                                    }
                                }
                            }
                        }
                        elseif ( $value_json->id === 'development' ){

                            if( property_exists($value_json, 'versions') ){
                                
                                if( is_array( $value_json->versions ) && count($value_json->versions) > 0 && property_exists($value_json->versions[0], 'timestamp') ){
                                    
                                    //-- check le timestamp rapport à celle en base, si non égal update la bdd + update de ufbt
                                    
                                    //-- on va récupérer les firmwares
                                    $sql_dev_firmware_req = $bdd_connexion->prepare('
                                        SELECT 

                                            * 
                                        
                                        FROM fzco_firmware 
                                        
                                        INNER JOIN fzco_depend ON depend_firmware_id = firmware_id
                                        INNER JOIN fzco_firmware_version ON depend_firmware_version_id = firmware_version_id

                                        WHERE firmware_is_active=1 AND firmware_version_is_active=1 AND firmware_version_type = "dev"
                                        AND firmware_id = :firm_id								
                                        ');

                                    $sql_dev_firmware_req->execute( ['firm_id' => $value_firm[ 'firmware_id' ] ]  );
                                    $sql_dev_firmware_res = $sql_dev_firmware_req->fetchAll();
                                    
                                    if( is_array( $sql_dev_firmware_res ) && count( $sql_dev_firmware_res ) > 0 ){
                                        if(  strtotime($sql_dev_firmware_res[0]['firmware_version_update_date']) !== $value_json->versions[0]->timestamp ){
                                            
                                            $sql_udpate_firmware_version_req = $bdd_connexion->prepare('
                                            UPDATE fzco_firmware_version SET firmware_version_update_date=:firm_date WHERE firmware_version_id=:firm_version_id AND firmware_version_type="dev"
                                            ');
                                            
                                            $sql_udpate_firmware_version_req->execute( [ 'firm_date' => date('Y-m-d H:i:s', $value_json->versions[0]->timestamp), 'firm_version_id' => $sql_dev_firmware_res[0]['firmware_version_id'] ]  );

                                            $state_dir_of_ufbt = $path_to_ufbt.'/fz_'. $value_firm[ 'firmware_ufbt_path' ].'_dev';

                                            //-- lancer les update ufbt via un task runner dédié idem que pour les compils
                                            file_put_contents($task_file_for_udpate, 'cd '.$path_to_ufbt.' && . bin/activate && rm -f .env && ufbt dotenv_create --state-dir '.$state_dir_of_ufbt.' && ufbt update --channel=dev --index-url='.$value_firm['firmware_url_update' ].' && rm .env && deactivate '.PHP_EOL ,FILE_APPEND);
                                        }			
                                    }		
                                }
                            }
                        }
                    }
                }
            
            }
        }
    }
}