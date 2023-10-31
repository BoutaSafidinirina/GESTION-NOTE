<?php 
    //démarer la session
    session_start();
    require_once("connexion_bdd.php");

     // Fetch data in descending order (lastest entry first)
    $result = mysqli_query($con, "SELECT * FROM personnes ORDER BY id DESC");
    $users = mysqli_query($con, "SELECT * FROM user ORDER BY id_user DESC");

    $reclamer = mysqli_query($con, 
        "SELECT * FROM reclamer 
            JOIN note ON note.id_note = reclamer.id_note 
            JOIN etudier ON etudier.id_etu = note.id_etu
            JOIN personnes ON etudier.id = personnes.id
            JOIN niveau ON etudier.id_niv = niveau.id_niv
            JOIN departements ON niveau.id_depart = departements.id_depart
            JOIN matieres ON note.id_mat = matieres.id_mat
            JOIN modules ON modules.id_mod = matieres.id_mod 
            WHERE statu='en attente'
            ORDER BY id_reclam DESC ")
        ;
    $row = mysqli_num_rows($result);
    $count = mysqli_num_rows($reclamer);
    if(!isset($_SESSION['user'])){
        $error = "vous devez d'abord vous connecter";
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    if ((time() - $_SESSION['timeout']) > 3) {
        unset($_SESSION['message']);
    }
    $user = $_SESSION['user'] ;// email de l'utilisateur
    $image = $_SESSION['image'];
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
                <a class="button-menu active" href="home.php">Accueil</a>
                <a class="button-menu" href="etudiants.php">Etudiant</a>
                <a class="button-menu" href="departement.php">Niveau/Departement</a>
                <a class="button-menu" href="module.php">Matière/Module</a>
                <a class="button-menu" href="note.php">Gerer note</a>
            </div>
           
            <div>
            <a href="deconnexion.php" class="Deconnexion_btn">Déconnexion</a>
            </div>
        </div>
        <div class="panel-body">
            <div class="home">
                <div class="image">
                    <img src="<?= $image ?>" width="300" height="300" />
                </div>
                <div class="message-home">
                    <p>BienVenu!</p>
                    <?php
                        if($count > 0){
                            echo "
                                <p>Des étudiants ont faits des réclamations,ils sont mise en attente</p>
                                <br><a href='#rec'>Verifier les listes</a>
                            ";
                        }else{
                            echo "<script> window.onload = function() {
                                generer();
                            }; </script>";
                            echo "
                                <p>Vous avez $row personnes inscrits maintenants</p>
                                <br><a href='#liste'>Verifier les listes</a>
                            ";
                            ;
                        }
                    ?>
                    

                </div>
            </div>
            <div id="liste" class="table-personne">
                <h2>Listes des personnes inscrits</h2>
                <div class="top-table">
                    <div class="create">
                        <button class="button-one">Ajouter</button>
                        <button class="button-one">Télecharger</button>
                    </div>
                    <div class="box">
                        <form name="search">
                            <input type="text" class="input" name="txt" onmouseout="this.value = ''; this.blur();">
                        </form>
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="fl-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prenom</th>
                                <th>Adresse</th>
                                <th>Telephone</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            ob_start();
                           
                            // Fetch the next row of a result set as an associative array
                            while ($us = mysqli_fetch_assoc($users)) {
                                while($res = mysqli_fetch_assoc($result)){
                                    if($res['id'] != $us['id']){
                                        echo "<tr>";
                                        echo "<td>".$res['nom']."</td>";
                                        echo "<td>".$res['prenom']."</td>";
                                        echo "<td>".$res['adresse']."</td>";
                                        echo "<td>".$res['tel']."</td>";
                                        echo "<td>".$res['email']."</td>";		
                                        echo "<td><a href=\"admin.php?id=$res[id]\">Admin</a> | 
                                        <a href=\"delete_personne.php?id=$res[id]\" onClick=\"return confirm('Are you sure you want to delete?')\">Supprimer</a></td>";
                                    }else{
                                        echo "<tr>";
                                        echo "<td>".$res['nom']."</td>";
                                        echo "<td>".$res['prenom']."</td>";
                                        echo "<td>".$res['adresse']."</td>";
                                        echo "<td>".$res['tel']."</td>";
                                        echo "<td>".$res['email']."</td>";		
                                        echo "<td><a href=\"delete_admin.php?id=$us[id_user]\">Retirer Admin</a> | 
                                        <a href=\"delete_personne.php?id=$res[id]\" onClick=\"return confirm('Are you sure you want to delete?')\">Supprimer</a></td>";
                                    }
                                }
                            }
                            ob_end_flush();
                        ?>
                        <tbody>
                    </table>
                </div>
            </div>
            <div id="rec" class="table-reclam">
                <h2>Listes des reclamation venu</h2>
                <div class="top-table">
                    <div class="create">
                        <button class="button-one">Télecharger</button>
                    </div>
                    <div class="box">
                        <form name="search">
                            <input type="text" class="input" name="txt" onmouseout="this.value = ''; this.blur();">
                        </form>
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="fl-table">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom et Prenom</th>
                                <th>Niveau</th>
                                <th>Matiere</th>
                                <th>Note</th>
                                <th>Réclamation</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            ob_start();
                            // Fetch the next row of a result set as an associative array
                            while ($res = mysqli_fetch_assoc($reclamer)) {
                                echo "<tr>";
                                echo "<td>".$res['num_mat']."</td>";
                                echo "<td>".$res['nom']." ".$res['prenom']."</td>";
                                echo "<td>".$res['niv']."/".$res['nom_depart']."</td>";
                                echo "<td>".$res['matiere']."/".$res['module']."</td>";
                                echo "<td>".$res['note']."</td>";
                                echo "<td>".$res['reclamation']."</td>";
                                echo "<td>".$res['date_reclam']."</td>";		
                                echo "<td><a href=\"valider.php?id=$res[id_reclam]\">Valider</a> | 
                                <a href=\"refuser.php?id=$res[id_reclam]\" onClick=\"return confirm('Are you sure you want to delete?')\">Refuser</a></td>";
                            }
                            ob_end_flush();
                        ?>
                        <tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function generer() {
            document.getElementById("rec").style.display = "none";
        }
    </script>
</body>
</html>