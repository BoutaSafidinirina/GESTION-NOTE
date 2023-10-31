<?php 
    ob_start();
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");
    $id = $_GET['id'];
    // Fetch data in descending order (lastest entry first)
    $result = mysqli_query($con, "SELECT * FROM matieres JOIN modules ON modules.id_mod = matieres.id_mod JOIN niveau ON modules.id_niv = niveau.id_niv JOIN departements ON niveau.id_depart = departements.id_depart WHERE id_mat=$id");
    $niveau = mysqli_query($con, "SELECT * FROM  niveau JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_niv DESC");
    $module = mysqli_query($con, "SELECT * FROM modules JOIN niveau ON modules.id_niv = niveau.id_niv JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_mod DESC");
    
    $resultData = mysqli_fetch_assoc($result);
    if(!isset($_SESSION['user'])){
        $error = "vous devez d'abord vous connecter";
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    if ((time() - $_SESSION['timeout']) > 3) {
        unset($_SESSION['message']);
    }
    $user = $_SESSION['user']; // nom de l'utilisateur
    if(isset($_POST['modifier-module'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($module) && $module != ""&& isset($niv) && $niv != ""){
            //inserer le message dans la base de données
            
            $niveau = (int)$niv;
            
            $idMod = $resultData["id_mod"];
            $req = mysqli_query($con, "UPDATE modules SET `module` = '$module',`id_niv` = $niveau WHERE `id_mod` = $idMod ");
        
            $req = mysqli_query($con , "INSERT INTO modules VALUES (NULL , '$module' ,$niveau )");
            //on actualise la page
            if($req){

                $_SESSION['message'] = "<p class='message_inscription'>Modification reussi!!</p>" ;
            
                header("location:module.php");
            }
        
        }else {
            $error = "Veuillez remplir tous les champs !" ;
        }
        
    }
    if(isset($_POST['modifier-matiere'])){
                           
        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($mod) && $mod != "" && isset($matiere) && $matiere != "" && isset($coef) && $coef != ""){
            //inserer le message dans la base de données
            
            $modu = (int)$mod;
           
            $req = mysqli_query($con, "UPDATE matieres SET `matiere` = '$matiere',`coef` = '$coef',`id_mod` = $modu  WHERE `id_mat` = $id ");
        
            //on actualise la page
            if($req){

                $_SESSION['message'] = "<p class='message_inscription'>Modification reussi!</p>" ;
                header("location:module.php");
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
                    <a class="button-menu " href="departement.php">Niveau/Departement</a>
                    <a class="button-menu active" href="module.php">Matière/Module</a>
                    <a class="button-menu" href="note.php">Gerer note</a>
                </div>
                <div>
                    <a href="deconnexion.php" class="Deconnexion_btn">Déconnexion</a>
                </div>
            </div>
            <div class="panel-body">
                <div id="ref1" class="form-ajout">
                    <form action="" method="POST" >                    
                        <h1>Ajout d' un module</h1>
                        <p class="message_error">
                            <?php 
                            //affichons l'erreur
                            if(isset($error)){
                                echo $error ;
                            }
                            ?>
                        </p>
                                    
                        <fieldset>
                            <legend>Module</legend>
                            <div>
                                <p  class="form-p">
                                    <select class="form-input" id="niv" name="niv">
                                        <?php while ($niv = mysqli_fetch_assoc($niveau)) { ?>
                                            <option value = "<?php echo $niv['id_niv']; ?>"  <?php if( $niv['id_niv']==$resultData["id_niv"]) echo 'selected="selected"'; ?>><?php echo $niv['niv']."/".$niv['nom_depart']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label class="form-label" for="niv">Niveau / Département</label>
                                </p>
                                
                                <p class="form-p">
                                    <input id="module"  class="form-input" type="text" value="<?php echo $resultData["module"]; ?>" name="module">
                                    <label class="form-label" for="module">Nom de la module</label>
                                </p>
                            </div>
                        </fieldset>
                        
                        <input type="submit" class="form-button" value="Modifier" name="modifier-module">
                    </form>
                </div>
                <div id="ref2" class="form-ajout">
                    <form action="" method="POST" >                    
                        <h1>Ajout d' un matière</h1>
                        <p class="message_error">
                            <?php 
                            //affichons l'erreur
                            if(isset($error)){
                                echo $error ;
                            }
                            ?>
                        </p>
                                    
                        <fieldset>
                            <legend>Matière</legend>
                            <div>
                                <p  class="form-p">
                                    <select class="form-input" id="mod" name="mod">
                                        <?php while ($modu = mysqli_fetch_assoc($module)) { ?>
                                            <option value = "<?php echo $mod['id_mod']; ?>"  <?php if( $mod['id_mod']==$resultData["id_mod"]) echo 'selected="selected"'; ?>><?php echo $modu['module']."/".$modu['niv']." ".$modu['nom_depart']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label class="form-label" for="mod">Module /Niveau  Département</label>
                                </p>
                                
                                <p class="form-p">
                                    <input id="matiere"  class="form-input" type="text" value="<?php echo $resultData["matiere"]; ?>" name="matiere">
                                    <label class="form-label" for="matiere">Nom de la matiere</label>
                                </p>
                                <p class="form-p">
                                    <input id="coef"  class="form-input" type="text" value="<?php echo $resultData["coef"]; ?>" name="coef">
                                    <label class="form-label" for="coef">Coefficient</label>
                                </p>
                            </div>
                        </fieldset>
                        
                        <input type="submit" class="form-button" value="Modifier" name="modifier-matiere">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>