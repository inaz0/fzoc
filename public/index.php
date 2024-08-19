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

class fzcoPDO extends PDO
{
    public function __construct(string $bdd_host, string $bdd_username, string $bdd_name, string $bdd_password = '', int $bdd_port = 3306 )
    {
                
        $dns = 'mysql:host='. $bdd_host .';port=' . $bdd_port .';dbname=' . $bdd_name;
        
        parent::__construct( $dns, $bdd_username, $bdd_password );
    }
}

$lang = 'fr';

$translation = [
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
            'error_other_field' => 'Vous n\'avez pas rempli tous les champs obligatoire.'
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
            'git_url_error' => 'The GitHub or GitLab URL was not conform. (ex. : https://github.com/inaz0/fzoc.git )',
            'error_other_field' => 'Missing mandatory fields.'
        ]
    ]
];

try{

    $bdd_connexion = new fzcoPDO( $bdd_host, $bdd_username, $bdd_name, $bdd_password );    
}
catch (PDOException $e){

    if( $debug === true ){

        var_dump( $e->getMessage() );
    }

    echo 'No database connection';
    die();
}

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

        if( !array_key_exists( $value_firm['firmware_id'], $firmware_allready_in_select ) ){

            $firmware_list_for_select .= '<option value="'.$value_firm['firmware_id'].'">'.$value_firm['firmware_name'].'</option>';
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

        //-- 1 on check l'url fourni
        if( filter_var( $_POST['git_url'] , FILTER_VALIDATE_URL) ){

            if( preg_match( '/https:\/\/(github|gitlab)\.com\/(.*)\.git/iu', $_POST['git_url'] ) ){

                $_POST['git_branch']      = intval($_POST['git_branch']);
                $_POST['firmware_target'] = intval($_POST['firmware_target']);

                //-- 2 plus simple on check les autres parametres
                if( ( $_POST['git_branch'] === 1 || $_POST['git_branch'] === 2 ) && !empty($_POST['firmware_target']) ){

                    //-- on pourra ensuite traiter nos données
                    $form_is_valid = true;
                }
                else{

                    $message .= PHP_EOL.$translation['fr']['error']['error_other_field'];    
                }
            }
            else{

                $message = $translation['fr']['error']['git_url_error'];    
            }
        }
        else{

            $message = $translation['fr']['error']['git_url_error'];
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
                    mkdir( $destination_dir, '755', true );
                    
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
                    shell_exec( 'cd '.escapeshellarg($destination_dir).' && git clone '.escapeshellarg( $_POST[ 'git_url' ]) .' new && chmod 777 -R new');

                    //-- lancer la compilation en nohup ou similaire
                    //-- @todo à compléter avec les infos firmware
                    ///-- a lancer en tache toutes les X minutes / secondes en bash avec un liste des action à réaliser
                    $content_of_dot_env = 'UFBT_HOME=/home/inazo/fz_momentum';

                    file_put_contents($task_list.'/'.str_replace('/','_',$generate_part_dest_dir).'.sh', 'cd '.$path_to_ufbt.' && source bin/activate && cd '.$destination_dir.'/new && echo "'.$content_of_dot_env.'" > .env && ufbt update --index-url=https://up.momentum-fw.dev/firmware/directory.json && ufbt > '.str_replace('/','_',$generate_part_dest_dir).'_result.txt' );

                    //-- on change les droits pour que le task runner puisse le consommer
                    chmod( $task_list.'/'.str_replace('/','_',$generate_part_dest_dir).'.sh', 0755);

                    //-- il faut insert en base que l'action va se jouer
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
    //-- 4 préparer les dossier

    
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
  <link rel="stylesheet" href="assets/css/custom.css">

  <link rel="icon" type="image/png" href="../../dist/images/favicon.png">
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

  <div class="section hero">
    <div class="container">
      <div class="row">
        <div class="">
          <h1 class="hero-heading"><?php echo $translation[ $lang ][ 'title_form' ]; ?></h1>

            <?php

            if( !empty($message) ){
                
                $type_message = 'message_error';

                if( $form_is_valid === true ){

                    $type_message = 'message_success';
                }
                
                echo '
                <div class="row">
                    <div class="column message_box message_error">
                        '. $message .'
                    </div>
                </div>';
            }

            ?>

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
                
            </div>
            <div class="row">
                <div class="one-third column value">&nbsp;</div>
                <div class="one-third column value"><input class="button button-primary" type="submit" name="compil" value="<?php echo $translation[ $lang ][ 'button_compil' ]; ?>" /></div>
                <div class="one-third column value">&nbsp;</div>
            </div>
          </form>
          
        </div>        
      </div>
    </div>
  </div>

  <div class="section values">
    <div class="container">
      <div class="row">
        <div class="one-third column value">
          <h2 class="value-multiplier">200</h2>
          <h5 class="value-heading">Compilation ce mois-ci</h5>
        </div>

	<div  class="one-third column value">
          <h2 class="value-multiplier">80 %</h2>
          <h5 class="value-heading">Pour le firmware : Momentum</h5>
	</div>

        <div class="one-third column value">
          <h2 class="value-multiplier">20000</h2>
          <h5 class="value-heading">Compilation depuis le début</h5>
        </div>
      </div>
    </div>
  </div>

  <div class="section get-help">
    <div class="container">
      <h3 class="section-heading">TABLE OF APP COMPIL</h3>
      <p class="section-description">Got a try with our demo version below:</p>
      <a class="button button-primary" href="https://demo-asvs.keikai.eu" target="_blank">Go to demo</a>
    </div>
  </div>

  <div class="section categories">
    <div class="container">
      <h3 class="section-heading">Want to support?</h3>
      <p class="section-description">Pay what you want, just paid a coffee here:</p>
      <p><a id="byMeACoffee" class="button button-primary" href="https://www.buymeacoffee.com/inazo" target="_blank">By me a coffee</a></p>
      
      <h3 class="section-heading">Follow me:</h3>
      <p>
        <a href="https://twitter.com/bsmt_nevers" target="_blank"><img src="assets/images/icons8-twitter-48.png" alt="Twitter" /></a>
        <a href="https://www.youtube.com/@kanjian_fr" target="_blank"><img src="assets/images/icons8-youtube-48.png" alt="Youtube" /></a>
      </p>
      <p>
        <a id="showLegalMentions" href="legal.php">Legal mentions</a>
      </p>
    </div>
  </div>

</body>
</html>