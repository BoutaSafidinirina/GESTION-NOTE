<?php 
    ob_start(); 
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");

    @$keywords =$_GET["keywords"];
    if(!empty(trim($keywords))){
        $result = mysqli_query($con, "SELECT * FROM  etudier JOIN personnes ON etudier.id = personnes.id JOIN niveau ON etudier.id_niv = niveau.id_niv JOIN departements ON departements.id_depart = niveau.id_depart 
        where nom like '%$keywords%' or prenom like '%$keywords%' or adresse like '%$keywords%' or num_mat like '%$keywords%' or tel like '%$keywords%' or email like '%$keywords%' or niv like '%$keywords%' or nom_depart like '%$keywords%'");
    }else{
        // Fetch data in descending order (lastest entry first)
        $result = mysqli_query($con, "SELECT * FROM etudier JOIN personnes ON etudier.id = personnes.id JOIN niveau ON etudier.id_niv = niveau.id_niv JOIN departements ON departements.id_depart = niveau.id_depart ORDER BY id_etu DESC");
    }
    $niveau = mysqli_query($con, "SELECT * FROM  niveau JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_niv DESC");
    $personne = mysqli_query($con, "SELECT * FROM  personnes ORDER BY id DESC");
    if(!isset($_SESSION['user'])){
        $error = "vous devez d'abord vous connecter";
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    $user = $_SESSION['user']; // email de l'utilisateur
    //envoi des valeurs
    if(isset($_POST['button-etu'])){

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($niv) && $niv != ""&& isset($person) && $person != "" && isset($num) && $num != ""){
            //inserer le message dans la base de données
            
            $per = (int)$person;
            $nive = (int)$niv;
            $req = mysqli_query($con , "INSERT INTO etudier VALUES (NULL , '$num' ,$per, $nive)");
            //on actualise la page
            
            if($req){
                // si le compte a été créer , créons une variable pour afficher un message dans la page de
                //connexion
                $_SESSION['message'] = "<p class='message_inscription'>Une personne vient d'être ajouter en tant que étudiants</p>" ;
                $_SESSION['timeout'] = time();
                // si l'utilisateur n'est pas connecté
                // redirection vers la page de connexion
                header("location:etudiants.php",  true,  301);
                exit;
            
            }else {
                //si non
                $error = "Inscription Echouée !";
            }
        }else {
            $error = "Veuillez remplir tous les champs !" ;
        }
        
    }
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
                        $_SESSION['message'] = "<p class='message_inscription'>Vous avez été créer une personne avec succès !</p>" ;
                        $_SESSION['timeout'] = time();
                        // si l'utilisateur n'est pas connecté
                        // redirection vers la page de connexion
                        header("location:etudiants.php",  true,  301);
                        exit;
                    
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
            <h2>Listes des etudiants</h2>
            <div class="message">
                <?php
                    if(isset($_SESSION['message'])){
                        echo $_SESSION['message'] ;
                        if ((time() - $_SESSION['timeout']) > 3) {
                            unset($_SESSION['message']);
                        }
                    }
                ?>
            </div>
            <div class="top-table">
                <div class="create">
                    <a href="#ref1" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Personne</a>
                    <a href="#ref2" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Etudiant</a>
                </div>
                <div class="box">
                    <form name="search" method="get" action="">
                        <input type="text" class="input" name="keywords" value="<?php $keywords ?>" onmouseout="this.value = ''; this.blur();">
                    </form>
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="fl-table">
                    <thead>
                        <tr>
                            <th>Numero matricule</th>
                            <th>Nom</th>
                            <th>Prenom</th>
                            <th>Adresse</th>
                            <th>Telephone</th>
                            <th>Email</th>
                            <th>Niveau</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                            // Fetch the next row of a result set as an associative array
                            while ($res = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>".$res['num_mat']."</td>";
                                echo "<td>".$res['nom']."</td>";
                                echo "<td>".$res['prenom']."</td>";
                                echo "<td>".$res['adresse']."</td>";
                                echo "<td>".$res['tel']."</td>";
                                echo "<td>".$res['email']."</td>";
                                echo "<td>".$res['niv'].'/'.$res['nom_depart']."</td>";		
                                echo "<td><a href=\"edit_etudiant.php?id=$res[id]\">Edit</a> | 
                                <a href=\"delete_etudiant.php?id=$res[id]\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
                            }
                        ?>
                   
                   <tbody>
                </table>
            </div>
            <div id="ref1" class="form-ajout">
                <form action="" method="POST" enctype="multipart/form-data" class="form_connexion_inscription" >
                    <h1>Incrire une personne</h1>
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
                            <div class="form-list-right" >
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
                </form>
            </div>
            <div id="ref2" class="form-ajout">
                <form action="" method="POST" >                    
                    <h1>Ajout d' un etudiant</h1>
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
                                        <option value = "<?php echo $pers['id']; ?>"><?php echo $pers['nom']." ".$pers['prenom']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="person">Nom et prénom</label>
                            </p>
                            <p  class="form-p">
                                <select class="form-input" id="niv" name="niv">
                                    <?php while ($niv = mysqli_fetch_assoc($niveau)) { ?>
                                        <option value = "<?php echo $niv['id_niv']; ?>"><?php echo $niv['niv']."/".$niv['nom_depart']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="niv">Niveau / Département</label>
                            </p>
                            <p class="form-p">
                                <input id="num"  class="form-input" type="text" name="num">
                                <label class="form-label" for="num">Numéro matricule</label>
                            </p>
                        </div>
                    </fieldset>
                    
                    <input type="submit" class="form-button" value="Ajouter" name="button-etu">
                </form>
            </div>
        </div>
    </div>
</body>
</html>