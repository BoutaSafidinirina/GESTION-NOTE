<?php
// Include the database connection file
require_once("connexion_bdd.php");

// Get id parameter value from URL
$id = $_GET['id'];

// Delete row from the database table
$result = mysqli_query($con, "DELETE FROM etudier WHERE id_etu = $id");

// Redirect to the main display page (etudiant.php in our case)
header("Location:etudiants.php");
