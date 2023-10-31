<?php 
    ob_start(); 
    //démarer la session
    session_start();
    require_once("connexion_bdd.php");

    $id = $_GET['id'];

    // Select data associated with this particular id
    $result = mysqli_query(
        $con, 
        "SELECT * FROM note 
        JOIN etudier ON etudier.id_etu = note.id_etu 
        JOIN personnes ON etudier.id= personnes.id 
        JOIN niveau ON etudier.id_niv = niveau.id_niv
        JOIN departements ON niveau.id_depart = departements.id_depart
        JOIN matieres ON note.id_mat = matieres.id_mat
        JOIN modules ON matieres.id_mod = modules.id_mod
        WHERE etudier.id_etu = $id
        ORDER BY id_note DESC"
    );
    $reclamer = mysqli_query($con, 
        "SELECT max(id_reclam) as max FROM reclamer 
        JOIN note ON note.id_note = reclamer.id_note 
        JOIN etudier ON etudier.id_etu = note.id_etu
        JOIN personnes ON etudier.id = personnes.id
        WHERE etudier.id_etu = $id GROUP BY etudier.id_etu")
        ;
    $row = mysqli_num_rows($reclamer);
    if(!isset($_SESSION['userEtu'])){
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    $user = $_SESSION['userEtu'];
    if(isset($_POST['send'])){
        // recuperons le message
        $message = $_POST['message'];
        $note = (int)$_POST['note']; 
        //connexion à la base de donnée
       
        include("connexion_bdd.php");
        //verifions si le champs n'est pas vide
        if(isset($message) && $message != "" && isset($note) && $note != ""){
            //inserer le message dans la base de données
            
            $req = mysqli_query($con , "INSERT INTO reclamer VALUES (NULL , $note ,'$message',NOW(),'en attente')");
            //on actualise la page
            
            if($req){
                // si le compte a été créer , créons une variable pour afficher un message dans la page de
                //connexion
                $_SESSION['message'] = "<p class='message_inscription'>Votre réclamation  a bien été envoyer avec succès !</p>" ;
                //redirection vers la page de connexion
                header("location:index_home.php?id=$id");
           
            }else {
                //si non
                header("location:index_home.php?id=$id");
            }
            
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
            <div>
                <span style="color:#612dff;padding:15px;font-size:15px;">
                    <?php
                        if($row >0){

                            $reclam =   mysqli_fetch_assoc($reclamer);
                            if($reclam["max"] != null){
                                $idre = $reclam['max'];
                                $query = mysqli_query($con, 
                                "  SELECT * FROM reclamer 
                                    WHERE id_reclam = $idre")
                                ;
                                $status = mysqli_fetch_assoc($query);
                                $val = $status["statu"];
                                echo "Votre reclamation est $val";
                            }
                        }
                        
                    ?>
                </span>
                <a href="deconnexionEtu.php" class="Deconnexion_btn">Déconnexion</a>
            </div>
        </div>
        <div class="panel-body">
            <h2>Listes des votre note actuelle</h2>
            <div class="top-table">
                <div class="create">
                    <a href="relever_note.php?id=<?=$id?>" class="button-one">Telecharger</a>
                </div>
            </div>
            <div class="table-wrapper">
                <?php
                    //affichons le message qui dit qu'un compte a été créer
                    if(isset($_SESSION['message'])){
                        echo $_SESSION['message'] ;
                        if ((time() - $_SESSION['timeout']) > 3) {
                            unset($_SESSION['message']);
                        }
                    }
                ?>
                <table class="fl-table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Matière</th>
                            <th>Coef°</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Fetch the next row of a result set as an associative array
                        while ($res = mysqli_fetch_assoc($result)) {
                            echo "<tr  id='generated'>";
                            echo "<td>".$res['module']."</td>";
                            echo "<td>".$res['matiere']."</td>";
                            echo "<td>".$res['coef']."</td>";
                            echo "<td>".$res['note']."</td>";
                            echo "<td><a href='#' onClick=\"generer($res[id_note])\">Reclamer</a></td></tr>";	
                        }
                    ?>
                    <tbody>
                </table>
            </div>
            <form action= "" class="send_message" method="POST">
                <input type="hidden" id="note" name="note">
                <textarea name="message" cols="30" rows="2" placeholder="Votre réclamation"></textarea>
                <input type="submit" value="Envoyé" name="send">
            </form>
        </div>
        <script>
            function generer(id){
                document.getElementById("generated").style.backgroundColor = "#ffd52d";
                document.getElementById("generated").style.color = "#fff";
                document.getElementById("note").value = id;
            }
        </script>
    </div>
</body>
</html>