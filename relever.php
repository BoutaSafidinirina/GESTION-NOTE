<?php 
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
        ORDER BY modules.id_mod DESC"
    );
    $etudiant = mysqli_query($con, 
        "SELECT * FROM etudier 
        JOIN personnes ON etudier.id = personnes.id 
        JOIN niveau ON etudier.id_niv = niveau.id_niv 
        JOIN departements ON departements.id_depart = niveau.id_depart 
        WHERE id_etu = $id");
    $generale = mysqli_query($con, 
        " SELECT AVG(moyenne) as generale FROM relever 
            WHERE id_etu = $id
        ");
    $resultats =  mysqli_fetch_assoc($generale);
    $personnes =  mysqli_fetch_assoc($etudiant);
    if(!isset($_SESSION['userEtu'])){
        // si l'utilisateur n'est pas connecté
        // redirection vers la page de connexion
        header("location:index.php");
    }
    $user = $_SESSION['userEtu'] // email de l'utilisateur
?>
<style>  
    *{
        font-size: 15px;
        box-sizing: border-box;
        font-family: 'Times New Roman', Times, serif;
    } 
    .generale{
        float:right;
        margin-top:20px;
    }
    .profil{
        margin-botoom:10px;
    }
    .profil p{
        margin-botoom:10px;
    }
    .table-wrapper{
        margin:70px;
    }
    .fl-table {
        border-radius: 5px;
        font-size: 12px;
        font-weight: normal;
        border: none;
        width:100%;
        border-collapse: collapse;
        white-space: nowrap;
        background-color: white;
    }

    .fl-table td, .fl-table th {
        text-align: center;
        padding: 8px;
    }

    .fl-table td {
        border-right: 1px solid #f8f8f8;
    }
    h2{
        text-align:center;
        color: #69d5f8;
        font-size:20px;
    }
    
    .fl-table thead th{
        font-size:18px;
        color: #ffffff;
        background: #9fa1a3;
    }

    .fl-table tr:nth-child(even) {
        heignt:20px;
        background: #F8F8F8;
    }
</style>
<div class="table-wrapper">
    <h2>RELEVE DES NOTES</h2>
    <div class="profil">
        <p><strong>Nom et prénom:</strong> <?= $user ?></p>
        <p><strong>Niveau:</strong> <?= $personnes["niv"]." / ".$personnes["nom_depart"] ?></p>
    </div>
    <div id="table">
        <table class="fl-table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Coef°</th>
                    <th>Note</th>
                    <th>Module</th>
                    <th>moyenne</th>
                    <th>Resultats</th>
                </tr>
            </thead>
            <tbody>
            <?php
                
                // Fetch the next row of a result set as an associative array
                while ($res = mysqli_fetch_assoc($result)) {
                    $id_mod = $res['id_mod'];
                    $moyenne = mysqli_query($con, 
                    " SELECT * FROM relever 
                        JOIN modules ON modules.id_mod = relever.id_mod 
                        WHERE id_etu = $id AND modules.id_mod = $id_mod
                        ORDER BY modules.id_mod DESC
                    ");
                    $moye = mysqli_fetch_assoc($moyenne);

                    echo "<tr  id='generated'>";
                    echo "<td>".$res['matiere']."</td>";
                    echo "<td>".$res['coef']."</td>";
                    echo "<td>".$res['note']."</td>";
                    echo "<td>".$moye['module']."</td>";
                    echo "<td>".$moye['moyenne']."</td>";
                    if( $moye['moyenne'] > 10){
                        echo "<td>Acquise</td></tr>";
                    }else{
                        echo "<td>Non Acquise</td></tr>";
                    }
                }
            ?>
            <tbody>
        </table>
    </div>
    <div class="generale" style="margin:70px 50px 0;">
        <p>Moyenne Generale: <strong style="font-size:18px;"><?= $resultats["generale"]?>/ 20</strong></p>
        <p>Resultat finale: <strong style="font-size:18px;"><?php
            if($resultats["generale"] > 10){
                echo "Admis";
            }else{
                echo "Autoriser à Redoubler";
            } ?></strong>
            </p>
    </div>
</div>

