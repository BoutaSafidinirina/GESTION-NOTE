<?php
// Include the database connection file
require_once("connexion_bdd.php");

// Get id parameter value from URL
$id = $_GET['id'];

// Updtae value of status on "refuser"
$result = mysqli_query($con , "INSERT INTO user VALUES (NULL, $id) ");

// Redirect to the main display page (etudiant.php in our case)
header("Location:home.php");