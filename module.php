<?php 
    ob_start(); 
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");
    @$keywords =$_GET["keywords"];
    if(!empty(trim($keywords))){
        $result = mysqli_query($con, " SELECT * FROM matieres JOIN modules ON modules.id_mod = matieres.id_mod JOIN niveau ON modules.id_niv = niveau.id_niv JOIN departements ON niveau.id_depart = departements.id_depart
        where  matiere like '%$keywords%' or coef like '%$keywords%' or module like '%$keywords%' or niv like '%$keywords%' or nom_depart like '%$keywords%'");
    }else{
    
    // Fetch data in descending order (lastest entry first)
    $result = mysqli_query($con, "SELECT * FROM matieres JOIN modules ON modules.id_mod = matieres.id_mod JOIN niveau ON modules.id_niv = niveau.id_niv JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_mat DESC");
   }
    
    if(!isset($_SESSION['user'])){
        $error = "vous devez d'abord vous connecter";
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    $user = $_SESSION['user'];
    if(isset($_POST['button-matiere'])){
                           
        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($mod) && $mod != "" && isset($matiere) && $matiere != "" && isset($coef) && $coef != ""){
            //inserer le message dans la base de données
            
            $modu = (int)$mod;
        
            $req = mysqli_query($con , "INSERT INTO matieres VALUES (NULL , '$matiere' ,'$coef', $modu)");
            //on actualise la page
            if($req){

                $_SESSION['message'] = "<p class='message_inscription'>Une matière a été créer avec succès !</p>" ;
                // si l'utilisateur n'est pas connecté
                // redirection vers la page de connexion
                header("location:module.php" , true,  301);
                exit;
            }
        
        }else {
            $error = "Veuillez remplir tous les champs !" ;
        }
        
    }
    if(isset($_POST['button-ajout'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($module) && $module != ""&& isset($niv) && $niv != ""){
            //inserer le message dans la base de données
            
            $niveau = (int)$niv;
            
            $req = mysqli_query($con , "INSERT INTO modules VALUES (NULL , '$module' ,$niveau )");
            //on actualise la page
            if($req){
                $_SESSION['message'] = "<p class='message_inscription'>Une module a été créer avec succès !</p>" ;
                // si l'utilisateur n'est pas connecté
                // redirection vers la page de connexion
                header("location:module.php",true,  301);
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
                    <a class="button-menu " href="departement.php">Niveau/Departement</a>
                    <a class="button-menu active" href="module.php">Matière/Module</a>
                    <a class="button-menu" href="note.php">Gerer note</a>
                </div>
                <div>
                    <a href="deconnexion.php" class="Deconnexion_btn">Déconnexion</a>
                </div>
            </div>
            <div class="panel-body">
                <h2>Listes des departements</h2>
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
                        <a href="#ref1" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Module</a>
                        <a href="#ref2" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Matière</a>
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
                                <th>#</th>
                                <th>Matière</th>
                                <th>Nom Module</th>
                                <th>Niveau/Departement</th>
                                <th>Coef°</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Fetch the next row of a result set as an associative array
                            while ($res = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td>".$res['matiere']."</td>";
                                echo "<td>".$res['module']."</td>";
                                echo "<td>".$res['niv'].'/'.$res['nom_depart']."</td>";
                                echo "<td>".$res['coef']."</td>";	
                                echo "<td><a href=\"edit_module.php?id=$res[id_mat]\">Edit</a> | 
                                <a href=\"delete_matiere.php?id=$res[id_mat]\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
                            }
                        ?>
                        <tbody>
                    </table>
                </div>
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
                                        <?php 
                                            $niveauF = mysqli_query($con, "SELECT * FROM  niveau JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_niv DESC");
                                            while ($niv = mysqli_fetch_assoc($niveauF)) { ?>
                                                <option value = "<?php echo $niv['id_niv']; ?>"><?php echo $niv['niv']."/".$niv['nom_depart']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label class="form-label" for="niv">Niveau / Département</label>
                                </p>
                                
                                <p class="form-p">
                                    <input id="module"  class="form-input" type="text" name="module">
                                    <label class="form-label" for="module">Nom de la module</label>
                                </p>
                            </div>
                        </fieldset>
                        
                        <input type="submit" class="form-button" value="Ajouter" name="button-ajout">
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
                                        <?php 
                                            $moduleF = mysqli_query($con, " SELECT * FROM modules JOIN niveau ON modules.id_niv = niveau.id_niv JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_mod DESC");
                                            while ($modu = mysqli_fetch_assoc($moduleF)) { ?>
                                            <option value = "<?php echo $modu['id_mod']; ?>"><?php echo $modu['module']."/".$modu['niv']." ".$modu['nom_depart']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <label class="form-label" for="mod">Module /Niveau  Département</label>
                                </p>
                                
                                <p class="form-p">
                                    <input id="matiere"  class="form-input" type="text" name="matiere">
                                    <label class="form-label" for="matiere">Nom de la matiere</label>
                                </p>
                                <p class="form-p">
                                    <input id="coef"  class="form-input" type="text" name="coef">
                                    <label class="form-label" for="coef">Coefficient</label>
                                </p>
                            </div>
                        </fieldset>
                        
                        <input type="submit" class="form-button" value="Ajouter" name="button-matiere">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>