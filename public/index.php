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
        'title_form' => 'Compiler une application'
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

          <form action="" method="post">
            <div class="container">
                <label for="git_url">URL du dépôt git : </label>
                <input type="text" name="git_url" id="git_url" />
            </div>
            <div class="container">
                <label for="firmware_target">Firmware cible : </label>
                <select name="firmware_target" id="firmware_target">
                        <?php echo $firmware_list_for_select; ?>
                </select>
            </div>
            <label for="git_branch">Firmware version (latest of) : </label>
            <select name="git_branch" id="git_branch">
                <option value="1">Release</option>
                <option value="2">Dev</option>
            </select>

            <input class="button button-primary" type="submit" name="compil" value="Launch compil!" />
          </form>
          
        </div>        
      </div>
    </div>
  </div>

  <div class="section values">
    <div class="container">
      <div class="row">
        <div class="one-third column value">
          <h2 class="value-multiplier">4.0.2</h2>
          <h5 class="value-heading">Oldest version</h5>
          <p class="value-description">Still avaible in our tool.</p>
        </div>

	<div  class="one-third column value">
          <h2 class="value-multiplier">See in action</h2>
          <h5 class="value-heading">Take a look to our:</h5>
	  <p><a  class="button button-primary" href="https://www.youtube.com/playlist?list=PLq_UnUtYZ15fUy2ilIvSS8ID1l9_XpjUW">Youtube videos</a></p>
	</div>

        <div class="one-third column value">
          <h2 class="value-multiplier">4.0.3</h2>
          <h5 class="value-heading">Current version</h5>
          <p class="value-description">Recommended version.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="section get-help">
    <div class="container">
      <h3 class="section-heading">Need to try the solution?</h3>
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