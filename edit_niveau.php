<?php 
    ob_start();
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");

    $id = $_GET['id'];
    // Fetch data in descending order (lastest entry first)
    $result = mysqli_query($con, "SELECT * FROM niveau LEFT JOIN departements ON niveau.id_depart = departements.id_depart WHERE id_niv = $id");
    $departement = mysqli_query($con, "SELECT * FROM departements");

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
    if(isset($_POST['button-mod'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($nomDep) && $nomDep != ""){
            //insertion dans la base de données
            $idDe = $resultData["id_depart"];
            $req = mysqli_query($con, "UPDATE departements SET `nom_depart` = '$nomDep'  WHERE `id_depart` = $idDe ");
            //on actualise la page
            if($req){

                $_SESSION['message'] = "<p class='message_inscription'>Modification reussi !</p>" ;
                
                header("location:departement.php" , true,  301);
                exit;
            }
        
        }else {
            $error = "Veuillez remplir tous les champs !" ;
        }
    }
    if(isset($_POST['modifier-niveau'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($depart) && $depart != ""&& isset($niv) && $niv != "" ){
            //inserer le message dans la base de données
            
            $depa = (int)$depart;
            
            $req = mysqli_query($con, "UPDATE niveau SET `niv` = '$niv',`id_depart` = $depa  WHERE `id_niv` = $id");
        
            //on actualise la page
            if($req){
               
                $_SESSION['message'] = "<p class='message_inscription'>Modification reussi !</p>" ;
                
                header("location:departement.php",true,  301);
                exit;
            }
        
        }else {
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
                <span><strong>BienVenu</strong> <?=$user?> </span>
            </div>
            <div class="button-menu">
                <a class="button-menu" href="home.php">Accueil</a>
                <a class="button-menu" href="etudiants.php">Etudiant</a>
                <a class="button-menu active" href="departement.php">Niveau/Departement</a>
                <a class="button-menu " href="module.php">Matière/Module</a>
                <a class="button-menu" href="note.php">Gerer note</a>
            </div>
           
            <div>
            <a href="deconnexion.php" class="Deconnexion_btn">Déconnexion</a>
            </div>
        </div>
        <div class="panel-body">
            <div id="ref1"   class="form-ajout">
                <form action="" method="POST" >                    
                    <h1>Modification d' un Départément</h1>
                    <p class="message_error">
                        <?php 
                        //affichons l'erreur
                        if(isset($error)){
                            echo $error ;
                        }
                        ?>
                    </p>
                                
                    <fieldset>
                        <legend>Département</legend>
                        <div>
                            <p class="form-p">
                                <input id="nomDep"  class="form-input" value="<?php echo $resultData["nom_depart"]; ?>" type="text" name="nomDep">
                                <label class="form-label" for="nomDep">Nom de la Département</label>
                            </p>
                        </div>
                    </fieldset>
                    
                    <input type="submit" class="form-button" value="Modifer" name="button-mod">
                </form>
            </div>
            <div id="ref2" class="form-ajout">
                <form action="" method="POST" >                    
                    <h1>Ajout d' un niveau</h1>
                    <p class="message_error">
                        <?php 
                        //affichons l'erreur
                        if(isset($error)){
                            echo $error ;
                        }
                        ?>
                    </p>
                                
                    <fieldset>
                        <legend>Niveau</legend>
                        <div>
                            <p  class="form-p">
                                <select class="form-input" id="depart" name="depart">
                                    <?php while ($dep = mysqli_fetch_assoc($departement)) { ?>
                                        <option value = "<?php echo $dep['id_depart']; ?>" <?php if( $dep['id_depart']==$resultData["id_depart"]) echo 'selected="selected"'; ?>><?php echo $dep['nom_depart']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="depart">Département</label>
                            </p  class="form-p">
                            
                            <p class="form-p">
                                <input id="niv" value="<?php echo $resultData["niv"]; ?>" class="form-input" type="text" name="niv">
                                <label class="form-label" for="matiere">Niveau</label>
                            </p>
                        </div>
                    </fieldset>
                    
                    <input type="submit" class="form-button" value="Modifier" name="modifier-niveau">
                </form>
            </div>
        </div>
</body>
</html>