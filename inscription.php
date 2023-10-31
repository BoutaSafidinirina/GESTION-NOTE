<?php 
  //démarer la session
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    if(isset($_POST['button_inscription'])){
        //si le formulaire est envoyé
        //se connecter à la base de donnée
        include "connexion_bdd.php";
        //extraire les infos du formulaire
        extract($_POST);
        
        if(  $_FILES['photo']['name'] != '' && 
            $_FILES['photo']['error'] == 0 && 
            isset($email) && isset($mdp1) && 
            isset($nom) && isset($prenom) && 
            isset($adresse) && isset($tel) 
            && $nom != "" && $prenom != ""
            && $adresse != "" && $tel != "" 
            && $email != "" && $mdp1 != ""
            && isset($mdp2) && $mdp2 != ""
        ){
            //récupération des informations sur la photo :
            $image = $_FILES['photo']['name'];
            $taille = $_FILES['photo']['size'];
            $tmp = $_FILES['photo']['tmp_name'];
            $type = $_FILES['photo']['type'];
            $erreur = $_FILES['photo']['error'];
            
            if($mdp2 != $mdp1){
                // s'ils sont differrent
                $error = "Les Mots de passes sont différents !";
            }else {
                    //Déplacement du fichier uploadé du répertoire temporaire à un répertoire du serveur.
                    if( $type == 'image/png' || $type == 'image/gif' || $type == 'image/jpeg' || $type == 'image/jpg'){
                        $nom_fichier = $_FILES['photo']['tmp_name'];
                        $photo = 'images/'.$image;
                        move_uploaded_file($nom_fichier, $photo);
                    }

                    //si non , verifions si l'email existe
                    $result = mysqli_query($con, "SELECT *  FROM  personnes WHERE email ='$email'");
                    $row_cnt = mysqli_num_rows($result);
                    
                    if($row_cnt == 0){
                        //si ça n'existe pas , créons le compte
                        
                        $req = mysqli_query($con , "INSERT INTO personnes VALUES (NULL,'$nom','$prenom','$adresse','$tel','$photo', '$email' , '$mdp1') ");
                        if($req){
                            // si le compte a été créer , créons une variable pour afficher un message dans la page de
                            //connexion
                            $_SESSION['message'] = "<p class='message_inscription'>Votre compte a été créer avec succès !</p>" ;
                            //redirection vers la page de connexion
                            header("Location:index.php") ;
                        
                        }else {
                            //si non
                            $error = "Inscription Echouée !";
                        }
                    }else {
                        //si ça existe
                        $error = "Cet Email existe déjà !";
                    }

            }
        }else{
            $error = "Veuillez remplir tous les champs !" ;
        }
        //verifions si les champs sont vides
        
    }
    ?>

    <form action="" method="POST" enctype="multipart/form-data" class="form_connexion_inscription form-inscrit" >
        <h1>INSCRIPTION</h1>
        <p class="message_error">
            <?php 
               //affichons l'erreur
               if(isset($error)){
                   echo $error ;
               }
            ?>
        </p>
                    
        <fieldset>
            <legend>Coordonnée</legend>
            <div class="row">
                <div class="form-list">
                    <p>
                        <input id="nom" name="nom" type="text" placeholder="Nom">
                        <label for= "nom">Nom</label>
                    </p>
                    <p>
                        <input id="prenom" name="prenom" type="text" placeholder="Prénom" >
                        <label for="prenom">Prénom</label>
                    </p>
                    <p>
                        <input id="adresse" name="adresse" type="text" placeholder="Adresse">
                        <label for="adresse">Adresse</label>
                    </p>
                    <p>
                        <input id="tel" name="tel" type="tel" placeholder="Mobile">
                        <label for="tel">Téléphone mobile</label>
                    </p>
                </div>
                <div class="form-list right" >
                    <p>
                        <input id="photo" name="photo" type="file">
                        <label for="photo">Photos d'identité</label>
                    </p>
                    <p>
                        <input type="email" name="email" placeholder="E-mail">
                        <label>Adresse Mail</label>
                     </p>
                    <p>
                        <input type="password" placeholder="Mots de passe" name="mdp1" class="mdp1">
                        <label>Mots de passe</label>
                    </p>
                    <p>
                        
                        <input type="password" name="mdp2" placeholder="Mots de passe" class="mdp2">
                        <label>Confirmer mots de passe</label>
                    </p>
                    
                </div>
            </div>
        </fieldset>
        
        <input type="submit" value="S'inscrire" name="button_inscription">
        <p class="link">Vous avez un compte ? <a href="index.php">Se connecter</a></p>
    </form>

    <!-- Relié notre page a notre fichier javascript -->
    <script src="script.js"></script>
    
</body>
</html>