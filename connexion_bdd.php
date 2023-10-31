<?php 
    $databaseHost = 'localhost';
    $databaseName = 'test';
    $databaseUsername = 'safidy';
    $databasePassword = 'Fion&123';

    //Connexion à la base de données
    $con = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName); 
   
    //gérer les accents et autres caractères français
    $req= mysqli_query($con , "SET NAMES UTF8");
    if(!$con){
        //si la connexion échoue , afficher :
        echo "Connexion échouée";
    }
?>