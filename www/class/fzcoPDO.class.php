<?php

class fzcoPDO extends PDO
{
    public function __construct(string $bdd_host, string $bdd_username, string $bdd_name, string $bdd_password = '', int $bdd_port = 3306 )
    {
                
        $dns = 'mysql:host='. $bdd_host .';port=' . $bdd_port .';dbname=' . $bdd_name;
        
        parent::__construct( $dns, $bdd_username, $bdd_password );
    }
}


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

?>