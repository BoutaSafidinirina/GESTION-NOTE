<?php 
    ob_start();
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");

    @$keywords =$_GET["keywords"];
    if(!empty(trim($keywords))){
        $result = mysqli_query($con, " SELECT * FROM niveau LEFT JOIN departements ON niveau.id_depart = departements.id_depart
        where  niv like '%$keywords%' or nom_depart like '%$keywords%'");
    }else{
        // Fetch data in descending order (lastest entry first)
        $result = mysqli_query($con, "SELECT * FROM niveau LEFT JOIN departements ON niveau.id_depart = departements.id_depart");
    }$departement = mysqli_query($con, "SELECT * FROM departements");
        if(!isset($_SESSION['user'])){
            $error = "vous devez d'abord vous connecter";
            // si l'utilisateur n'est pas connecté
            // redirection vers la page de connexion
            header("location:index.php");
    }
    $user = $_SESSION['user'];//l'utilisateur
    //envoi des messages
    if(isset($_POST['button-ajout'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($nomDep) && $nomDep != ""){
            //insertion dans la base de données
            $req = mysqli_query($con , "INSERT INTO departements VALUES (NULL , '$nomDep' )");
            //on actualise la page
            if($req){

                $_SESSION['message'] = "<p class='message_inscription'>Une département a été créer avec succès !</p>" ;
                $_SESSION['timeout'] = time();
                // si l'utilisateur n'est pas connecté
                // redirection vers la page de connexion
                header("location:departement.php",true,  301);
                exit;
            }
        
        }else {
            $error = "Veuillez remplir tous les champs !" ;
        }
        
    }
    if(isset($_POST['button-niveau'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée
    
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($depart) && $depart != ""&& isset($niv) && $niv != "" ){
            //inserer le message dans la base de données
            
            $depa = (int)$depart;
            
            $req = mysqli_query($con , "INSERT INTO niveau VALUES (NULL , '$niv' , $depa )");
            //on actualise la page
            if($req){

                $_SESSION['message'] = "<p class='message_inscription'>Un niveau a été créer avec succès !</p>" ;
                $_SESSION['timeout'] = time();
                // si l'utilisateur n'est pas connecté
                // redirection vers la page de connexion
                header("location:departement.php" , true,  301);
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
                    <a href="#ref1" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Département</a>
                    <a href="#ref2" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Niveau</a>
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
                            <th>Niveau</th>
                            <th>Nom départements</th>
                            <th>Action</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Fetch the next row of a result set as an associative array
                            while ($res = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td>".$res['niv']."</td>";
                                echo "<td>".$res['nom_depart']."</td>";	
                                echo "<td><a href=\"edit_niveau.php?id=$res[id_niv]\">Edit</a> | 
                                <a href=\"delete_niveau.php?id=$res[id_niv]\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
                            }
                        ?>
                    <tbody>
                </table>
            </div>
            <div id="ref1" class="form-ajout">
                <form action="" method="POST" >                    
                    <h1>Ajout d' un Départément</h1>
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
                                <input id="nomDep"  class="form-input" type="text" name="nomDep">
                                <label class="form-label" for="nomDep">Nom de la Département</label>
                            </p>
                        </div>
                    </fieldset>
                    
                    <input type="submit" class="form-button" value="Ajouter" name="button-ajout">
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
                                        <option value = "<?php echo $dep['id_depart']; ?>"><?php echo $dep['nom_depart']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="depart">Département</label>
                            </p  class="form-p">
                            
                            <p class="form-p">
                                <input id="niv" class="form-input" type="text" name="niv">
                                <label class="form-label" for="matiere">Niveau</label>
                            </p>
                        </div>
                    </fieldset>
                    <input type="submit" class="form-button" value="Ajouter" name="button-niveau">
                </form>
            </div>
        </div>
</body>
</html>