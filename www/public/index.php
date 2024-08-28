<?php

/*
 * Author: Inazo
 * Website: https://www.kanjian.fr
 * GitHub: https://github.com/inaz0/fzoc
 * Youtube Channel: https://youtube.com/@kanjian_fr
 * X: https://www.x.com/bsmt_nevers
 * Instagram: https://www.instagram.com/kanjian_fr/
 * Buy me a coffee: https://buymeacoffee.com/inazo
 */

require_once('../config.php');

$lang          = 'fr';
$is_fr_current = 'current';
$is_en_current = '';

$cloudflare_turnstile = '';
$cloudflare_api       = '<script type="text/javascript" src="https://challenges.cloudflare.com/turnstile/v0/api.js"></script>';
$captcha_is_solved    = false;

if( $is_active_cloudflaire_turnstile === true ){

    $cloudflare_turnstile = '<div class="cf-turnstile" data-sitekey="'. $cloudflare_turnstile_sitekey .'"></div>';
}

if( !empty($_GET['lang']) ){

    switch( $_GET['lang'] ){

        case 'en':
            $lang          = 'en';
            $is_en_current = 'current';
            $is_fr_current = '';
            break;
        
        default:

            $lang          = 'fr';
            $is_fr_current = 'current';
            $is_en_current = '';
            break;
    }
}

$version_type = [ 1 => 'release', 2 => 'dev' ];
$translation  = [
    'fr' =>
    [
        'title_form'       => 'Compiler une application',
        'git_url'          => 'URL du dépôt git (ex. : https://github.com/inaz0/fzoc.git) : ',
        'target_firmware'  => 'Firmware cible : ',
        'version_firmware' => 'Version du firmware (la dernière) : ',
        'button_compil'    => 'Démarrer la compilation !',
        'error'            => 
        [
            'git_url_error'     => 'L\'url du dépôt GitHub ou GitLab n\'est pas conforme. (ex. : https://github.com/inaz0/fzoc.git )',
            'error_other_field' => 'Vous n\'avez pas rempli tous les champs obligatoire.',
            'captcha_error'     => 'Vous devez valider le captcha.'
        ],
        'success'                 => 'La compilation va bientôt commencer rafraichissez la page régulièrement pour voir le résultat apparaitre ci-dessous.',
        'title_table_app'         => 'Applications compilées',
        'date_format'             => 'd/m/Y<b\r>H\hi',
        'compilation_this_month'  => 'Compilation ce mois-ci',
        'most_firmware'           => 'Le plus pour le firmware :',
        'compilation_since_start' => 'Compilation depuis le début',
        'legend'                  => [

            'title'             => 'Légende des statuts :',
            'status_pending'    => 'la compilation est en cours.',
            'status_success'    => 'le fap a été compilé, il est téléchargeable durant 30 jours.',
            'status_deleted'    => 'le fap a été supprimé automatiquement au bout de 30 jours.',
            'status_impossible' => 'Impossible de compiler l\'application, en général c\'est un app_id déjà intégré au firmware officiel.',
        
        ],
        'data_table' => [
            'app_name' => 'Application',
            'date'     => 'Date',
            'status'   => 'Statut',
            'firmware' => 'Firmware',
            'download' => 'Téléchargement',
            'search'   => 'Rechercher une application : ',
            'info'     => 'Affichage de _START_ à _END_ sur _TOTAL_ entrée(s)'
        ],
        'bottom_site'  => [
            'title_subscribe' => 'Soutenez le projet !',
            'follow_me'       => 'Suivez-moi !',
            'link_legal'      => 'Mentions légales'
        ]

    ],
    'en' => 
    [
        'title_form'       => 'Application compilation',
        'git_url'          => 'URL of git repository (ex: https://github.com/inaz0/fzoc.git) : ',
        'target_firmware'  => 'Target version: ',
        'version_firmware' => 'Version firmware  (latest of): ',
        'button_compil'    => 'Start compilation!',
        'error'            => 
        [
            'git_url_error'     => 'The GitHub or GitLab URL was not conform. (ex. : https://github.com/inaz0/fzoc.git )',
            'error_other_field' => 'Missing mandatory fields.',
            'captcha_error'     => 'You does to validate the captcha.'
        ],
        'success'                 => 'The compilation will start soon, refresh the page regularly to see the result appear below.',
        'title_table_app'         => 'Compiled applications',
        'date_format'             => 'Y-m-d H:i:s',
        'compilation_this_month'  => 'Compilation this month',
        'most_firmware'           => 'The more for the firmware:',
        'compilation_since_start' => 'Compilation from the beginning',
        'legend'                  => [

            'title'             => 'Status legend:',
            'status_pending'    => 'compilation is in progress.',
            'status_success'    => 'the fap has been compiled, it is downloadable for 30 days.',
            'status_deleted'    => 'the fap was automatically deleted after 30 days.',
            'status_impossible' => 'Unable to compile the application, usually it is an app_id already integrated into the official firmware.',
        
        ],
        'data_table' => [
            'app_name' => 'Application',
            'date'     => 'Date',
            'status'   => 'Status',
            'firmware' => 'Firmware',
            'download' => 'Download',
            'search'   => 'Search an application: ',
            'info'     => 'Showing _START_ to _END_ of _TOTAL_ _ENTRIES-TOTAL_'
        ],
        'bottom_site'  => [
            'title_subscribe' => 'Support the project!',
            'follow_me'       => 'Follow me!',
            'link_legal'      => 'Legal mentions'
        ]
    ]
];


//-- on va lister les firmwares actifs
$all_firmware_req = $bdd_connexion->prepare('
    SELECT 

        * 
    
    FROM fzco_firmware 
    
    INNER JOIN fzco_depend ON depend_firmware_id = firmware_id
    INNER JOIN fzco_firmware_version ON depend_firmware_version_id = firmware_version_id

    WHERE firmware_is_active=1 AND firmware_version_is_active=1
    
    ');

$firmware_list_for_select    = '';
$firmware_allready_in_select = [];

try{

    $all_firmware_req->execute();
    $all_firmware_res = $all_firmware_req->fetchAll();

    foreach( $all_firmware_res ?? [] as $key_firm => $value_firm ){

        if( !array_key_exists( $value_firm['firmware_id'], $firmware_allready_in_select ) && $value_firm[ 'firmware_version_type' ] === 'release' ){

            $firmware_list_for_select .= '<option value="'.$value_firm['firmware_id'].'">'.$value_firm['firmware_name'].' - '. $value_firm['firmware_version_name'] .'</option>';
            $firmware_allready_in_select[ $value_firm['firmware_id'] ] = 1;
        }        
    }
}
catch(PDOException $e){

    if( $debug === true ){

        echo $e->getMessage();
    }

    echo 'No firmware available';

    die();
}

//-- stockage du message
$message     = '';

//-- par défaut le formulaire est considéré invalide
$form_is_valid = false;

//-- gestion des soumissions du formulaire
//-- @todo add captcha cloudflare
try{

    if( count($_POST) > 0 && array_key_exists('git_url', $_POST) && array_key_exists('firmware_target', $_POST) && array_key_exists('git_branch', $_POST) && array_key_exists('compil', $_POST) ){

        //-- 0 si on a cloudflare turnstile d'activé on va contrôler
        if( $is_active_cloudflaire_turnstile === true ){

            //-- 1 récupérer le .fam et le contrôler
            $curl = curl_init();

            $post_data = [
                'secret' => $cloudflare_turnstile_secretkey,
                'response' => $_POST[ 'cf-turnstile-response' ]
            ];

            // fixe l'URL et les autres options appropriées
            $options_curl = array(
                CURLOPT_URL            => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                CURLOPT_HEADER         => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS      => 0,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $post_data
                
            );

            curl_setopt_array( $curl, $options_curl );

            //-- récupération du contenu
            $response_curl      = curl_exec($curl);
            $response_code_curl = curl_getinfo( $curl, CURLINFO_RESPONSE_CODE );   
            
            if( $response_code_curl === 200 ){

                $response_challenge = json_decode( $response_curl );
                if( $response_challenge->success === true ){

                    $captcha_is_solved = true;
                }
            }
        }
        else{

            $captcha_is_solved = true;
        }

        //-- 1 on check l'url fourni
        if( filter_var( $_POST['git_url'] , FILTER_VALIDATE_URL) && $captcha_is_solved === true ){ 

            if( preg_match( '/https:\/\/(github|gitlab)\.com\/(.*)\.git/iu', $_POST['git_url'] ) ){

                $_POST['git_branch']      = intval($_POST['git_branch']);
                $_POST['firmware_target'] = intval($_POST['firmware_target']);

                //-- 2 plus simple on check les autres parametres
                if( ( $_POST['git_branch'] === 1 || $_POST['git_branch'] === 2 ) && !empty($_POST['firmware_target']) ){

                    //-- on pourra ensuite traiter nos données
                    $form_is_valid = true;
                }
                else{

                    $message .= PHP_EOL.$translation[ $lang ]['error']['error_other_field'];    
                }
            }
            else{

                $message = $translation[ $lang ]['error']['git_url_error'];    
            }
        }
        else{

            if( $captcha_is_solved === false ){

                $message = $translation[ $lang ]['error']['captcha_error'];
            }
            else{

                $message = $translation[ $lang ]['error']['git_url_error'];
            }            
        }
    }
}
catch(PDOException $e){

    if( $debug === true ){

        echo $e->getMessage();
    }

    echo 'An error was occured.';

    die();
}

//-- on va pouvoir regarder si on traite ou pas la suite
if( $form_is_valid === true ){

    //-- 1 récupérer le .fam et le contrôler
    $curl = curl_init();

    //-- on modifie les URL pour arriver en raw sur le fichier en fonction de github ou gitlab
    $array_url_base = ['/(https:\/\/github\.com)\/(.*)(\.git)/iu', '/(https:\/\/gitlab\.com\/.*)(\.git)/'];
    $array_url_raw  = ['https://raw.githubusercontent.com/$2/master/application.fam', '$1/-/raw/main/application.fam'];
    $raw_url        = preg_replace( $array_url_base, $array_url_raw, $_POST['git_url'], 1);

    // fixe l'URL et les autres options appropriées
    $options_curl = array(
        CURLOPT_URL            => $raw_url,
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS      => 0
    );

    curl_setopt_array( $curl, $options_curl );

    //-- récupération du contenu
    $response_curl      = curl_exec($curl);
    $response_code_curl = curl_getinfo( $curl, CURLINFO_RESPONSE_CODE );   

    curl_close( $curl );
    //-- on ne veut que du 200
    if( $response_code_curl === 200 ){

        //-- on va checker la structure du fam
        if( preg_match_all( '/(\s*)?(App\()\s*(appid=")([a-z0-9_-]*)(",)\s*(name=")(.*)(",)(.*)\s*/mi', $response_curl, $matches )){

            $the_app_id   = '';
            $the_app_name = '';

            if( is_array( $matches[ 4 ] ) && count( $matches[ 4 ] ) > 0 ){

                $the_app_id = $matches[ 4 ][ 0 ];
            }

            if( is_array( $matches[ 7 ] ) && count( $matches[ 7 ] ) > 0 ){

                $the_app_name = $matches[ 7 ][ 0 ];
            }

            //-- 2 contrôler que le repo est présent ou non en base
            $sql_application_check = $bdd_connexion->prepare('
                SELECT application_id 
                FROM fzco_application 
                WHERE application_url_git = :git_url 
            ');

            try{

                $sql_application_check->execute( [ 'git_url' => $_POST[ 'git_url' ] ] );
                $sql_application_check_res = $sql_application_check->fetchAll();

                if( is_array($sql_application_check_res) ){

                    $starting_time_process  = time();
                    $generate_part_dest_dir = hash( 'md5', $_POST['git_url'] ).'/'.$starting_time_process;
                    $destination_dir        =  __DIR__.'/../gits/'.$generate_part_dest_dir;

                    //-- création d'un dossier pour cloner
                    mkdir( $destination_dir, 0777, true );
                    
                    //-- création de l'application
                    if( count($sql_application_check_res) === 0 ){

                        $sql_add_application = $bdd_connexion->prepare('
                        INSERT INTO fzco_application (application_name, application_appid,application_url_git)
                         VALUES ( :app_name, :app_id, :app_url_git )');

                        $sql_add_application->execute( [ 'app_name' => $the_app_name, 'app_id' => $the_app_id, 'app_url_git' => $_POST['git_url'] ] );

                        $application_id = $bdd_connexion->lastInsertId();
                    }
                    else{

                        $application_id = $sql_application_check_res[ 0 ];
                    }
                    
                    //-- new pour pouvoir maitriser le nom du répo...
                    shell_exec( 'cd '.escapeshellarg($destination_dir).' && git clone '.escapeshellarg( $_POST[ 'git_url' ]) .' new && chmod -R 777 '.__DIR__.'/../gits/');

                    //-- on va récupérer les informations du firmware
                    //-- 2 contrôler que le repo est présent ou non en base
                    $sql_firmware_info = $bdd_connexion->prepare('
                        SELECT * 
                        FROM fzco_firmware 
                        INNER JOIN fzco_depend ON depend_firmware_id = fzco_firmware.firmware_id
                        INNER JOIN fzco_firmware_version ON fzco_depend.depend_firmware_version_id = fzco_firmware_version.firmware_version_id 
                        WHERE firmware_id = :firmware_id AND firmware_version_type = :version_type AND firmware_version_is_active = 1 AND firmware_is_active = 1
                    ');

                    $sql_firmware_info->execute( [ 'firmware_id' => $_POST['firmware_target'], 'version_type' => $version_type[ $_POST[ 'git_branch' ] ] ] );
                    
                    $sql_firmware_info_res = $sql_firmware_info->fetchAll();

                    if( is_array($sql_firmware_info_res) && count($sql_firmware_info_res) === 1 ){
                  
                        //-- on va ajouter la demande de compilation à la file d'attente
                        //-- @todo à compléter avec les infos firmware
                        $state_dir_of_ufbt = $path_to_ufbt.'/fz_'. $sql_firmware_info_res[ 0 ][ 'firmware_ufbt_path' ];

                        //-- si la branch est la dev on change la branch à utiliser
                        if( $_POST[ 'git_branch' ] === 2 ){

                            $state_dir_of_ufbt .= '_dev' ;
                            $ufbt_args          = '--channel dev';
                        }
                        else{

                            $state_dir_of_ufbt .= '_release';
                            $ufbt_args          = '';
                        }

                        //-- list des commandes qui vont être jouées par le task runner, plus simple à maintenir et faire évoluer
                        $task_detail = [
                            'cd '.$path_to_ufbt,
                            '. bin/activate',
                            'cd '. $destination_dir .'/new ',
                            'ufbt dotenv_create --state-dir '.$state_dir_of_ufbt.' ',
                          // to put every morning  'ufbt update '. $ufbt_args .' --index-url='. $sql_firmware_info_res[0][ 'firmware_url_update' ] .' ',
                            'ufbt ',
                            'mkdir -p '.$fap_path.$generate_part_dest_dir.'/ ',
                            'mv '.$destination_dir.'/new/dist/*.fap '.$fap_path.$generate_part_dest_dir.'/',
                            //'rm -rf '.$destination_dir.'/',
                        ];

                        file_put_contents($task_list. '/'. str_replace('/','_',$generate_part_dest_dir) .'.sh', implode(' && ', $task_detail ) );

                        //-- on change les droits pour que le task runner puisse le consommer
                        chmod( $task_list.'/'.str_replace('/','_',$generate_part_dest_dir).'.sh', 0755);

                        //-- il faut insert en base que l'action va se jouer
                        $sql_add_compiled = $bdd_connexion->prepare('
                        INSERT INTO fzco_compiled (compiled_firmware_version_id, compiled_application_id,compiled_date,compiled_path_fap,compiled_status)
                        VALUES ( :compiled_firmware_version_id, :compiled_application_id, :compiled_date, :compiled_path_fap, "pending" )');

                        //@todo reprendre le bon id du firmware
                        $sql_add_compiled->execute( [ 
                            'compiled_firmware_version_id' => $sql_firmware_info_res[ 0 ][ 'firmware_version_id' ],
                            'compiled_application_id'      => $application_id[ 0 ],
                            'compiled_date'                => date('Y-m-d H:i:s', $starting_time_process), 
                            'compiled_path_fap'            => $generate_part_dest_dir 
                            ] );

                    }
                    //@todo mettre le message que la compile va bientot commmencer
                    $form_is_valid = true;
                    $message       = $translation[ $lang ][ 'success' ];
                }
            }
            catch(PDOException $e){

                if( $debug === true ){

                    echo $e->getMessage();
                }

                echo 'Error application listing';

                die();
            }
        }
        else{

            echo 'error fam';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>

  <meta charset="utf-8">
  <title>FlipperZero online compilator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="assets/css/normalize.css">
  <link rel="stylesheet" href="assets/css/skeleton.css">
  <link rel="stylesheet" href="assets/css/dataTables.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  
  <script type="text/javascript" src="assets/js/jquery-3.7.1.min.js"></script>
  <script type="text/javascript" src="assets/js/dataTables.min.js" ></script>
  <?php echo $cloudflare_api; ?>

  <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->


  <div class="section header">
    <div class="container">
      <div class="row">
        <div class="">
          <h1 class="hero-heading">FlipperZero Online Compilator</h1><br />
          <div class="header_by">By </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section hero">
    <div class="container">
      <div class="row">
        <div class="lang"><a href="?lang=fr" class="<?php echo $is_fr_current;?>">FR</a> | <a href="?lang=en" class="<?php echo $is_en_current;?>">EN</a></div>
        <div class="">
          <h1 class="hero-heading"><?php echo $translation[ $lang ][ 'title_form' ]; ?></h1>

          <form action="" method="post">
            
                <label for="git_url"><?php echo $translation[ $lang ][ 'git_url' ]; ?></label>
                <input type="text" name="git_url" id="git_url" />
            
                <div class="row">
                    <div class="six columns">
                        <label for="firmware_target"><?php echo $translation[ $lang ][ 'target_firmware' ]; ?></label>
                        <select name="firmware_target" id="firmware_target">
                            <?php echo $firmware_list_for_select; ?>
                        </select>
                    </div>
                    <div class="six columns">
                        <label for="git_branch"><?php echo $translation[ $lang ][ 'version_firmware' ]; ?></label>
                        <select name="git_branch" id="git_branch">
                            <option value="1">Release</option>
                            <option value="2">Dev</option>
                        </select>   
                    </div>
                </div>

                <?php echo $cloudflare_turnstile; ?>
                
            </div>
            <div class="row">
                <div class="one-third column value">&nbsp;</div>
                <div class="one-third column value"><input class="button button-primary" type="submit" name="compil" value="<?php echo $translation[ $lang ][ 'button_compil' ]; ?>" /></div>
                <div class="one-third column value">&nbsp;</div>
            </div>
          </form>
                    
            <?php

            if( !empty($message) ){
                
                $type_message = 'message_error';

                if( $form_is_valid === true ){

                    $type_message = 'message_success';
                }
                
                echo '
                <div class="row">
                    <div class="column message_box '. $type_message .'">
                        '. $message .'
                    </div>
                </div>';
            }

            ?>

        </div>        
      </div>
    </div>
  </div>

  <div class="section values">
  <div class="container">
        <h3 class="section-heading"><?php echo $translation[ $lang ][ 'title_table_app' ]; ?></h3>

        <?php

        //-- récupération de la liste des applications
        $sql_all_application_compiled = $bdd_connexion->prepare('
            SELECT * 
            FROM fzco_application 
            INNER JOIN fzco_compiled ON fzco_compiled.compiled_application_id = fzco_application.application_id
            INNER JOIN fzco_firmware_version ON fzco_firmware_version.firmware_version_id = fzco_compiled.compiled_firmware_version_id
            INNER JOIN fzco_depend ON fzco_depend.depend_firmware_version_id = fzco_firmware_version.firmware_version_id
            INNER JOIN fzco_firmware ON fzco_firmware.firmware_id = fzco_depend.depend_firmware_id
        ');

        $sql_all_application_compiled->execute();
        
        $sql_all_application_compiled_res = $sql_all_application_compiled->fetchAll();

        $data_for_datatable         = [];
        $nb_application_since_start = 0;
        $nb_application_this_month  = 0;
        $nb_most_firmware           = 0;
        $most_firmware_name         = '';

        if( is_array( $sql_all_application_compiled_res ) && count( $sql_all_application_compiled_res ) > 0 ){

            $nb_application_since_start = count( $sql_all_application_compiled_res );

            $current_month_date = date('Y-m');

            foreach( $sql_all_application_compiled_res as $an_app ){

                if( $current_month_date === date( 'Y-m' , strtotime( $an_app[ 'compiled_date' ] ) ) ){

                    $nb_application_this_month++;
                }

                if( $an_app[ 'compiled_status' ] === 'success' ){

                    $link_for_dl = $an_app[ 'compiled_path_fap' ] .'/'. $an_app[ 'application_appid' ] .'.fap';
                }
                else{

                    $link_for_dl = null;
                }

                $data_for_datatable[] = '
                    [
                        "'. $an_app[ 'application_name' ] .'<br /><span class=\"secondary_info\"><a href=\"'. $an_app[ 'application_url_git' ] .'\" target=\"_blank\">Git of apps</a></span>",
                        "'. date( $translation[ $lang ][ 'date_format' ] , strtotime( $an_app[ 'compiled_date' ] ) ) .'",
                        "<span class=\"'. $an_app[ 'compiled_status' ].'\">'. $an_app[ 'compiled_status' ].'</span>",
                        "'. $an_app[ 'firmware_name' ] .'<br /><span class=\"secondary_info\">'. $an_app[ 'firmware_version_name' ] .' - '. $an_app[ 'firmware_version_type' ] .'</span>",
                        "'. $link_for_dl. '" 
                    ]
                ';
            }
        }

        $sql_most_firmware = $bdd_connexion->prepare('
        SELECT 
        COUNT(*) as nb_compil,
        firmware_name
        FROM fzco_application 
        INNER JOIN fzco_compiled ON fzco_compiled.compiled_application_id = fzco_application.application_id
        INNER JOIN fzco_firmware_version ON fzco_firmware_version.firmware_version_id = fzco_compiled.compiled_firmware_version_id
        INNER JOIN fzco_depend ON fzco_depend.depend_firmware_version_id = fzco_firmware_version.firmware_version_id
        INNER JOIN fzco_firmware ON fzco_firmware.firmware_id = fzco_depend.depend_firmware_id
        GROUP BY firmware_id
        ORDER BY nb_compil DESC
        LIMIT 1
        ');

        if( $sql_most_firmware->execute() === true ){

            $sql_most_firmware_res = $sql_most_firmware->fetchAll();

            if( is_array( $sql_most_firmware_res ) && count( $sql_most_firmware_res ) === 1 ){

                $nb_most_firmware   = ceil( ( $sql_most_firmware_res[ 0 ][ 'nb_compil' ] * 100 ) / $nb_application_since_start );
                $most_firmware_name = $sql_most_firmware_res[ 0 ][ 'firmware_name' ];
            }
        }
        

        ?>

        <script type="text/javascript" >
        $( document ).ready(function() {
            let table = new DataTable('#list_of_applications', {
                order:[[1,'desc']],
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 25,
                pagingType: 'simple_numbers',
                lengthChange: false,
                data: [
                    <?php echo implode( ',', $data_for_datatable ); ?>
                ],
                columns: [
                    { title: '<?php echo $translation[ $lang ][ 'data_table' ][ 'app_name' ]; ?>' },
                    { title: '<?php echo $translation[ $lang ][ 'data_table' ][ 'date' ]; ?>' },
                    { title: '<?php echo $translation[ $lang ][ 'data_table' ][ 'status' ]; ?>' },
                    { title: '<?php echo $translation[ $lang ][ 'data_table' ][ 'firmware' ]; ?>' },
                    { title: '<?php echo $translation[ $lang ][ 'data_table' ][ 'download' ]; ?>' }
                ],
                columnDefs: [
                {
                    targets: 4,
                    className: 'download_datatable',
                    render: function (data, type, row, meta) {

                        if( data !== '' ) {

                            return '<a href="faps/' + data + '" class="link_for_download"><?php echo $translation[ $lang ][ 'data_table' ][ 'download' ]; ?></a>';
                        }
                        else{

                            return '';
                        }
                    }
                },
                {
                    className: 'status_datatable',
                    targets: 2
                }
                ],
                language: {
                    info: "<?php echo $translation[ $lang ][ 'data_table' ][ 'info' ]; ?>",
                    search: "<?php echo $translation[ $lang ][ 'data_table' ][ 'search' ]; ?>"
                }
            });
        });
        </script>
        <table id="list_of_applications" class="stripe" style="width:100%;"></table>
        <p class="legend_explain status_datatable"><?php echo $translation[ $lang ][ 'legend' ][ 'title' ]; ?><br />
            
            <span class="pending">pending</span> : <?php echo $translation[ $lang ][ 'legend' ][ 'status_pending' ]; ?><br />
            <span class="success">success</span> : <?php echo $translation[ $lang ][ 'legend' ][ 'status_success' ]; ?><br />
            <span class="deleted">deleted</span> : <?php echo $translation[ $lang ][ 'legend' ][ 'status_deleted' ]; ?><br />
            <span class="impossible">impossible</span> : <?php echo $translation[ $lang ][ 'legend' ][ 'status_impossible' ]; ?><br />
        </p>
    </div>
    
  </div>

  <div class="section get-help">
    <div class="container">
      <div class="row">
        <div class="one-third column value">
          <h2 class="value-multiplier"><?php echo $nb_application_this_month; ?></h2>
          <h5 class="value-heading"><?php echo $translation[ $lang ][ 'compilation_this_month' ]; ?></h5>
        </div>
        <div  class="one-third column value">
            <h2 class="value-multiplier"><?php echo $nb_most_firmware; ?> %</h2>
            <h5 class="value-heading"><?php echo $translation[ $lang ][ 'most_firmware' ]; ?> <?php echo $most_firmware_name; ?></h5>
        </div>
        <div class="one-third column value">
          <h2 class="value-multiplier"><?php echo $nb_application_since_start; ?></h2>
          <h5 class="value-heading"><?php echo $translation[ $lang ][ 'compilation_since_start' ]; ?></h5>
        </div>
      </div>
    </div>
  </div>
  
  <div class="section categories">
    <div class="container">
      <h3 class="section-heading"><?php echo $translation[ $lang ][ 'bottom_site' ][ 'title_subscribe' ]; ?></h3>
      <p><a id="byMeACoffee" class="button button-primary" href="https://www.buymeacoffee.com/inazo" target="_blank">By me a coffee</a></p>
      
      <h3 class="section-heading"><?php echo $translation[ $lang ][ 'bottom_site' ][ 'follow_me' ]; ?></h3>
      <p>
        <a href="https://twitter.com/bsmt_nevers" target="_blank"><img src="assets/images/icons8-twitter-48.png" alt="Twitter" /></a>
        <a href="https://www.youtube.com/@kanjian_fr" target="_blank"><img src="assets/images/icons8-youtube-48.png" alt="Youtube" /></a>
        <a href="https://www.instagram.com/kanjian_fr/" target="_blank"><img src="assets/images/icons8-instagram-48.png" alt="Instagram" /></a>
        <a href="https://kanjianfr.etsy.com/" target="_blank"><img src="assets/images/icons8-etsy-48.png" alt="Etsy" /></a>
        <a href="https://github.com/inaz0/" target="_blank"><img src="assets/images/icons8-github-48.png" alt="GitHub" /></a>
      </p>
      <p>
        <a id="showLegalMentions"><?php echo $translation[ $lang ][ 'bottom_site' ][ 'link_legal' ]; ?></a>
      </p>
    </div>
  </div>

  <script type="text/javascript">

$( document ).ready(function() {
	$( "#showLegalMentions" ).bind( "click", function() {
	
	
	  let linkPosition = $( "#showLegalMentions" ).offset();  
	
	  $( "#legalMentions" ).last().offset({ top: (linkPosition.top*0.80), left: (linkPosition.left*0.15) });
	  $("#legalMentions").show();
	});
	
	$("#closeMe").bind( "click", function() {
	
	  $("#legalMentions").hide();
	});
});
</script>


<div id="legalMentions">
    <div id="closeMe">X close</div>
    <div class="container">
      <div class="row">
        <div class="one-half column">
    
    <h2>Legal mentions</h2>
    
    <p>
	    Editor: Alexandre JOLY<br />
	    58000 Nevers
	</p>
	
	<p>
        Hosting: OVH - Roubaix
    </p>
    
    <p>
        Contact: https://www.kanjian.fr/me-contacter
    </p>
    <h3>RGPD</h3>
    
    <p>No data was collected in this site, no cookie used, no external service used.</p>
    
    </div>
        <div class="one-half column">
    
    <h3>Iconography</h3>
    <p>
	    Background image credit: <a href='https://fr.freepik.com/vecteurs/fond-minimaliste'>Fond minimaliste vecteur créé par freepik - fr.freepik.com</a><br />
	    Computer image credit:<a href='https://fr.freepik.com/psd/ordinateur'>Ordinateur PSD créé par rawpixel.com - fr.freepik.com</a><br />
	
	    Twitter logo: <a target="_blank" href="https://icons8.com/icon/13963/twitter">Twitter</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a><br />
	    Youtube logo: <a target="_blank" href="https://icons8.com/icon/13983/youtube">YouTube</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a><br />
        Instagram logo : <a target="_blank" href="https://icons8.com/icon/Xy10Jcu1L2Su/instagram">Instagram</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a><br />
        Etsy logo : <a target="_blank" href="https://icons8.com/icon/MQ-HLKLCGrJn/etsy">Etsy</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a><br />
        Download logo: <a target="_blank" href="https://icons8.com/icon/103169/downloading-updates">Download</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a><br />
        GitHub logo: <a target="_blank" href="https://icons8.com/icon/AZOZNnY73haj/github">GitHub</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a>
    </p>    
           </div>
        </div>
    </div>
</div>

</body>
</html>