<?php 
    ob_start();
    //démarer la session
    session_start();
    // Include the database connection file
    require_once("connexion_bdd.php");

    @$keywords =$_GET["keywords"];
    if(!empty(trim($keywords))){
        $result = mysqli_query($con, " SELECT * FROM note 
        JOIN etudier ON etudier.id_etu = note.id_etu 
        JOIN personnes ON etudier.id= personnes.id 
        JOIN niveau ON etudier.id_niv = niveau.id_niv
        JOIN departements ON niveau.id_depart = departements.id_depart
        JOIN matieres ON note.id_mat = matieres.id_mat
        JOIN modules ON matieres.id_mod = modules.id_mod
        where nom like '%$keywords%' or matiere like '%$keywords%' or module like '%$keywords%' or note like '%$keywords%' or prenom like '%$keywords%' or adresse like '%$keywords%' or num_mat like '%$keywords%' or tel like '%$keywords%' or email like '%$keywords%' or niv like '%$keywords%' or nom_depart like '%$keywords%'");
    }else{
     
    // Fetch data in descending order (lastest entry first)
        $result = mysqli_query(
            $con, 
            " SELECT * FROM note 
            JOIN etudier ON etudier.id_etu = note.id_etu 
            JOIN personnes ON etudier.id= personnes.id 
            JOIN niveau ON etudier.id_niv = niveau.id_niv
            JOIN departements ON niveau.id_depart = departements.id_depart
            JOIN matieres ON note.id_mat = matieres.id_mat
            JOIN modules ON matieres.id_mod = modules.id_mod
            ORDER BY id_note DESC"
        );
    }
    $etudiant = mysqli_query($con, "SELECT * FROM etudier JOIN personnes ON etudier.id = personnes.id ORDER BY id_etu DESC");
    $matiere = mysqli_query($con, "SELECT * FROM matieres JOIN modules ON modules.id_mod = matieres.id_mod JOIN niveau ON modules.id_niv = niveau.id_niv JOIN departements ON niveau.id_depart = departements.id_depart ORDER BY id_mat DESC");
    
    if(!isset($_SESSION['user'])){
        $error = "vous devez d'abord vous connecter";
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    $user = $_SESSION['user']; // email de l'utilisateur
    //envoi des valeurs
    if(isset($_POST['button-note'])){
        // recuperons le message

        extract($_POST);
        
        //connexion à la base de donnée

        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($nom) && $nom != "" && isset($mati) && $mati != ""  && isset($note) && $note != "" ){
            //inserer le message dans la base de données
            
            $mat = (int)$mati;
            $etud = (int)$nom;
            $matier = mysqli_query($con , "SELECT * FROM note WHERE id_mat =$mat AND id_etu = $etud");
            $row_cnt = mysqli_num_rows($matier);
            if($row_cnt == 0){
                $req = mysqli_query($con , "INSERT INTO note VALUES (NULL , $note ,$mat, $etud)");
                //on actualise la page
                if($req){
                    $modulo = mysqli_query($con , "SELECT * FROM matieres  JOIN modules ON matieres.id_mod = modules.id_mod  WHERE id_mat =$mat");
                    $resultModu= mysqli_fetch_assoc($modulo);
                    $id_mod = $resultModu["id_mod"];

                    $query1 = mysqli_query($con, 
                        " SELECT id_etu,modules.id_mod,SUM(note*coef)/SUM(coef) as moy_module FROM note 
                            JOIN matieres ON note.id_mat = matieres.id_mat 
                            JOIN modules ON matieres.id_mod = modules.id_mod 
                            WHERE id_etu = $etud and modules.id_mod = $id_mod
                            GROUP BY modules.id_mod;");
                    $resultData = mysqli_fetch_assoc($query1);
                    
                    $id_etu = $resultData["id_etu"];
                    $moyenne=$resultData["moy_module"];

                    $query2 = mysqli_query($con, " SELECT *  FROM relever WHERE id_mod =$id_mod AND id_etu = $id_etu");
                    $resultData2 = mysqli_fetch_assoc($query2);
                    $row_rel = mysqli_num_rows($query2);

                    if($row_rel > 0){
                        $id_relev =$resultData2["id_relev"];
                        $result = mysqli_query($con, "UPDATE relever SET `moyenne` = '$moyenne'  WHERE `id_relev` = $id_relev");
                        
                        $_SESSION['message'] = "<p class='message_inscription'>Une note a été créer avec succès !</p>" ;
                        $_SESSION['timeout'] = time();
                        // si l'utilisateur n'est pas connecté
                        // redirection vers la page de connexion
                        header("location:note.php",true,  301);
                        exit;
                        
                    }else{
                        $result = mysqli_query($con , "INSERT INTO relever VALUES (NULL , '$moyenne' ,$id_mod, $etud)");
                        
                        $_SESSION['message'] = "<p class='message_inscription'>Une note a été créer avec succès !</p>" ;
                        $_SESSION['timeout'] = time();
                        // si l'utilisateur n'est pas connecté
                        // redirection vers la page de connexion
                        header("location:note.php",true,  301);
                        exit;
                    }
                }else{
                    $error = "Inscription echoué!" ;
                }
            }else{
                $req = mysqli_query($con , "UPDATE note SET `note` = $note  WHERE `id_mat` =$mat AND `id_etu`=$etud");
                //on actualise la page
                if($req){
                    $modulo = mysqli_query($con , "SELECT * FROM matieres  JOIN modules ON matieres.id_mod = modules.id_mod  WHERE id_mat =$mat");
                    $resultModu= mysqli_fetch_assoc($modulo);
                    $id_mod = $resultModu["id_mod"];

                    $query1 = mysqli_query($con, 
                        " SELECT id_etu,modules.id_mod,SUM(note*coef)/SUM(coef) as moy_module FROM note 
                            JOIN matieres ON note.id_mat = matieres.id_mat 
                            JOIN modules ON matieres.id_mod = modules.id_mod 
                            WHERE id_etu = $etud AND modules.id_mod = $id_mod
                            GROUP BY modules.id_mod;");
                    $resultData = mysqli_fetch_assoc($query1);

                    $id_etu = $resultData["id_etu"];
                    $moyenne=$resultData["moy_module"];

                    $query2 = mysqli_query($con, " SELECT *  FROM relever WHERE id_mod =$id_mod AND id_etu = $id_etu");
                    $resultData2 = mysqli_fetch_assoc($query2);
                    $row_rel = mysqli_num_rows($query2);
                    
                    if($row_rel > 0){
                        $id_relev =$resultData2["id_relev"];
                        $result = mysqli_query($con, "UPDATE relever SET `moyenne` = '$moyenne'  WHERE `id_relev` = $id_relev");
                        
                        $_SESSION['message'] = "<p class='message_inscription'>Une note a été créer avec succès !</p>" ;
                        // si l'utilisateur n'est pas connecté
                        // redirection vers la page de connexion
                        header("location:note.php");
                                                    
                    }else{
                        $result = mysqli_query($con , "INSERT INTO relever VALUES (NULL , '$moyenne' ,$id_mod, $etud)");

                        $_SESSION['message'] = "<p class='message_inscription'>Une note a été créer avec succès !</p>" ;
                        // si l'utilisateur n'est pas connecté
                        // redirection vers la page de connexion
                        header("location:note.php");
                    }
                }else{
                    $error = "Inscription echoué!" ;
                }
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
                <a class="button-menu" href="module.php">Matière/Module</a>
                <a class="button-menu active" href="note.php">Gerer note</a>
            </div>
           
            <div>
            <a href="deconnexion.php" class="Deconnexion_btn">Déconnexion</a>
            </div>
        </div>
        <div class="panel-body">
            <h2>Listes des notes</h2>
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
                    <a href="#ref" class="button-one"><i class="fa fa-plus" aria-hidden="true"></i> Ajouter</a>
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
                            <th>Matricule</th>
                            <th>Nom et Prénom</th>
                            <th>Nom Module</th>
                            <th>Matière</th>
                            <th>Coef°</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            ob_start();
                            // Fetch the next row of a result set as an associative array
                            while ($res = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td>".$res['niv'].'/'.$res['nom_depart']."</td>";
                                echo "<td>".$res['num_mat']."</td>";
                                echo "<td>".$res['nom'].' '.$res['prenom']."</td>";
                                echo "<td>".$res['module']."</td>";
                                echo "<td>".$res['matiere']."</td>";
                                echo "<td>".$res['coef']."</td>";
                                echo "<td>".$res['note']."</td>";		
                                echo "<td><a href=\"delete_note.php?id=$res[id_note]\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
                            }
                            ob_end_flush();
                        ?>
                    
                    <tbody>
                </table>
            </div>
            <div id="ref" class="form-ajout">               
               <form action="" method="POST" >                    
                    <h1>Ajout d' un note</h1>
                    <p class="message_error">
                        <?php 
                            //affichons l'erreur
                            if(isset($error)){
                                echo $error ;
                            }
                        ?>
                    </p>
                                
                    <fieldset>
                        <legend>Note</legend>
                        <div>
                            <p  class="form-p">
                                <select class="form-input" id="nom" name="nom">
                                    <?php while ($etu = mysqli_fetch_assoc($etudiant)) { ?>
                                        <option value = "<?php echo $etu['id_etu']; ?>"><?php echo $etu['num_mat']."/".$etu['nom']." ".$etu['prenom']; ?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="nom">Numéro matricule / Nom et prénom</label>
                            </p>
                            <p  class="form-p">
                                <select class="form-input" id="mati" name="mati">
                                <?php while ($mat = mysqli_fetch_assoc($matiere)) { ?>
                                        <option value = "<?php echo $mat['id_mat']; ?>"><?php echo $mat['matiere']."/".$mat['module']." (".$mat['niv']." /".$mat['nom_depart'].")";?></option>
                                    <?php } ?>
                                </select>
                                <label class="form-label" for="mati">Matiere</label>
                            </p>
                            
                            <p class="form-p">
                                <input id="note"  class="form-input" type="number" name="note">
                                <label class="form-label" for="note">Note</label>
                            </p>
                        </div>
                    </fieldset>
                    
                    <input type="submit" class="form-button" value="Ajouter" name="button-note">
                </form>
            </div>
        </div>

      
</body>
</html>