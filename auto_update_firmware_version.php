<?php

require_once('config.php');


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

    var_dump( $response_curl );
}