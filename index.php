
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
    <title>Gestion de note</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php 
       if(isset($_POST['button_con'])){
           //si le formulaire est envoyé
           //se connecter à la base de donnée
           include "connexion_bdd.php";
           //extraire les infos du formulaire
           extract($_POST);
           //verifions si les champs sont vides
           if(isset($email) && isset($mdp1) && $email != "" && $mdp1 != ""){
                
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                //verifions si les identifiants sont justes
                $result = mysqli_query($con, "SELECT *  FROM personnes WHERE email ='$email' AND mdp1 ='$mdp1'");
                $row_cnt = mysqli_num_rows($result);
                
                if($row_cnt > 0){
                    
                    $query1 = mysqli_query($con, " SELECT *  FROM etudier JOIN personnes ON personnes.id = etudier.id WHERE personnes.email ='$email' AND personnes.mdp1 ='$mdp1'");
                    $row1 = mysqli_num_rows($query1);

                    $query2 = mysqli_query($con, " SELECT *  FROM user JOIN personnes ON personnes.id = user.id WHERE personnes.email ='$email' AND personnes.mdp1 ='$mdp1'");
                    $row2 = mysqli_num_rows($query2);


                    if($row1 > 0){
                        $resultData = mysqli_fetch_assoc($query1);
                        
                        $nom = $resultData['nom'].' '.$resultData['prenom'];

	                    $id = $resultData['id_etu'];
                        //Création d'une session qui contient l'email
                        $_SESSION['userEtu'] = $nom ;
                        $_SESSION['image'] = $resultData['photo'];
                        $_SESSION['timeout'] = time();
                                    
                        //redirection vesr la page d'accueil d'un etudiant
                        header("location:index_home.php?id=$id");
                        // detruire la variable du message d'inscription
                        
                        unset($_SESSION['message']);
                        
                    }else if($row2 > 0){
                            
                            $resultData = mysqli_fetch_assoc($query2);
                            
                            $nom = $resultData['nom'].' '.$resultData['prenom'];

                            //Création d'une session qui contient l'email
                            $_SESSION['user'] = $nom ;
                            $_SESSION['image'] = $resultData['photo'];
                            $_SESSION['timeout'] = time();
                            
                            //redirection vesr la page d'accueil de l'admin
                            header("location:home.php");

                            // detruire la variable du message d'inscription
                            unset($_SESSION['message']);
                    }else{
                        //si non
                        $error = "Vous n'êtes pas authoriser à se connecter!";
                    }
                    
               }else {
                   //si non
                   $error = "Email ou Mots de passe incorrecte(s) !";
               }
           }else {
               //si les champs sont vides
               $error = "Veuillez remplir tous les champs !" ;
           }
       }
    ?>
    <form action=""  method="POST" class="form_connexion">
        <h1>Connexion</h1>
        <?php
           //affichons le message qui dit qu'un compte a été créer
           if(isset($_SESSION['message'])){
               echo $_SESSION['message'] ;
           }
        ?>
        <p class="message_error">
            <?php 
               //affichons l'erreur
               if(isset($error)){
                   echo $error ;
               }
            ?>
        </p>
        <label>Adresse Mail</label>
        <input type="email" name="email">
        <label>Mots de passe</label>
        <input type="password" name="mdp1">
        <input type="submit" value="Connexion" name="button_con">
        <p class="link">Vous n'avez pas de compte ? <a href="inscription.php">Créer un compte</a></p>
    </form>
    
</body>
</html>