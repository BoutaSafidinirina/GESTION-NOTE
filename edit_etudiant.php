<?php 
    ob_start();
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");

    $id = $_GET['id'];
    // Fetch data in descending order (lastest entry first)
    $result = mysqli_query($con, "SELECT * FROM etudier JOIN personnes ON etudier.id = personnes.id JOIN niveau ON etudier.id_niv = niveau.id_niv JOIN departements ON departements.id_depart = niveau.id_depart WHERE personnes.id = $id");
    $niveau = mysqli_query($con, "SELECT * FROM  niveau JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_niv DESC");
    $personne = mysqli_query($con, "SELECT * FROM  personnes ORDER BY id DESC");

    $resultData = mysqli_fetch_assoc($result);
    if(!isset($_SESSION['user'])){
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    if ((time() - $_SESSION['timeout']) > 3) {
        unset($_SESSION['message']);
    }
    $user = $_SESSION['user']; // email de l'utilisateur
    if(isset($_POST['modi'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($niv) && $niv != ""&& isset($person) && $person != "" && isset($num) && $num != ""){
            //inserer le message dans la base de données
            
            $per = (int)$person;
            $nive = (int)$niv;

            $req = mysqli_query($con, "UPDATE etudier SET `num_mat` = '$num',`id` = $per,`id_niv` = $nive WHERE `id_etu` = $id");
            
            //on actualise la page
            
            if($req){
                // si le compte a été créer , créons une variable pour afficher un message dans la page de
                //connexion
                $_SESSION['message'] = "<p class='message_inscription'>Modification reussi!</p>" ;
                //redirection vers la page de connexion
                header("location:etudiants.php") ;
            
            }else {
                //si non
                $error = "Inscription Echouée !";
            }
        }else {
            $error = "Veuillez remplir tous les champs !" ;
        }
        
    }
    if(isset($_POST['modifier-etu'])){
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
                    
                    $idPersonne = $resultData["id"];
                    
                    $req = mysqli_query($con, "UPDATE personnes SET `nom` ='$nom', `prenom` ='$prenom',`adresse` ='$adresse',`tel` ='$tel',`email` = '$email' ,`mdp1` = '$mdp1' WHERE `id` = $idPersonne");
                    
                    if($req){
                        // si le compte a été créer , créons une variable pour afficher un message dans la page de
                        //connexion
                        $_SESSION['message'] = "<p class='message_inscription'>Modification reussi!!</p>" ;
                        //redirection vers la page de connexion
                        header("Location:etudiants.php") ;
                    
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
    ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?=$user?> | Connected</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="panel">
        <div class="button-email">
            <div class="logo">
                <span>Bienvenu <?=$user?> </span>
            </div>
            <div class="button-menu">
                <a class="button-menu" href="home.php">Accueil</a>
                <a class="button-menu active" href="etudiants.php">Etudiant</a>
                <a class="button-menu" href="departement.php">Niveau/Departement</a>
                <a class="button-menu" href="module.php">Matière/Module</a>
                <a class="button-menu" href="note.php">Gerer note</a>
            </div>
           
            <div>
            <a href="deconnexion.php" class="Deconnexion_btn">Déconnexion</a>
            </div>
        </div>
       
        <div class="panel-body">
            <div class="form-ajout">
                <form action="" method="POST" enctype="multipart/form-data" class="form_connexion_inscription" >
                    <h1>Modifier une personne</h1>
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
                        <div class="row-list">
                            <div class="form-list">
                                <p>
                                    <input id="nom" name="nom" type="text" value="<?php echo $resultData["nom"]; ?>" placeholder="Nom">
                                    <label for= "nom">Nom</label>
                                </p>
                                <p>
                                    <input id="prenom" name="prenom" type="text" value="<?php echo $resultData["prenom"]; ?>" placeholder="Prénom" >
                                    <label for="prenom">Prénom</label>
                                </p>
                                <p>
                                    <input id="adresse" name="adresse" type="text" value="<?php echo $resultData["adresse"]; ?>" placeholder="Adresse">
                                    <label for="adresse">Adresse</label>
                                </p>
                                <p>
                                    <input id="tel" name="tel" type="tel" value="<?php echo $resultData["tel"]; ?>" placeholder="Mobile">
                                    <label for="tel">Téléphone mobile</label>
                                </p>
                            </div>
                            <div class="form-list-right" >
                                <p>
                                    <input id="photo" name="photo" value="<?php echo $resultData["photo"]; ?>" type="file">
                                    <label for="photo">Photos d'identité</label>
                                </p>
                                <p>
                                    <input type="email" name="email" value="<?php echo $resultData["email"]; ?>"placeholder="E-mail">
                                    <label>Adresse Mail</label>
                                </p>
                                <p>
                                    <input type="password" placeholder="Mots de passe" value="<?php echo $resultData["mdp1"]; ?>" name="mdp1" class="mdp1">
                                    <label>Mots de passe</label>
                                </p>
                                <p>
                                    
                                    <input type="password" name="mdp2" placeholder="Mots de passe" value="<?php echo $resultData["mdp1"]; ?>" class="mdp2">
                                    <label>Confirmer mots de passe</label>
                                </p>
                                
                            </div>
                        </div>
                    </fieldset>
                    <input type="submit" value="modifier" name="modifier-etu">
                </form>
            </div>
            <div id="ref2" class="form-ajout">
                <form action="" method="POST" >                    
                    <h1>Modifier d' un etudiant</h1>
                    <p class="message_error">
                        <?php 
                        //affichons l'erreur
                        if(isset($error)){
                            echo $error ;
                        }
                        ?>
                    </p>
                                
                    <fieldset>
                        <legend>Etudiant</legend>
                        <div>
                            <p  class="form-p">
                                <select class="form-input" id="person" name="person">
                                    <?php while ($pers = mysqli_fetch_assoc($personne)) { ?>
                                        <option value = "<?php echo $pers['id']; ?>"  <?php if( $pers['id']==$resultData["id"]) echo 'selected="selected"'; ?>><?php echo $pers['nom']." ".$pers['prenom']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="person">Nom et prénom</label>
                            </p>
                            <p  class="form-p">
                                <select class="form-input" id="niv" name="niv">
                                    <?php while ($niv = mysqli_fetch_assoc($niveau)) { ?>
                                        <option value = "<?php echo $niv['id_niv']; ?>"  <?php if( $niv['id_niv']==$resultData["id_niv"]) echo 'selected="selected"'; ?>><?php echo $niv['niv']."/".$niv['nom_depart']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="niv">Niveau / Département</label>
                            </p>
                            <p class="form-p">
                                <input id="num"  class="form-input" type="text" value="<?php echo $resultData["num_mat"]; ?>" name="num">
                                <label class="form-label" for="num">Numéro matricule</label>
                            </p>
                        </div>
                    </fieldset>
                    
                    <input type="submit" class="form-button" value="Modifier" name="modi">
                </form>
            </div>
        </div>
    </div>
</body>
</html>