<?php
include("vues/v_sommaire.php");
$action = $_REQUEST['action'];
$idUtilisateur = $_SESSION['idUtilisateur'];
switch($action){

	case 'suiviDePaiement':
        $fiches = $pdo->FichesValidees();

        // Une demande de suivi de fiche
        if(isset($_GET['fiche'])){
            $infos = explode('-', $_GET['fiche']);
            if(isset($infos[0]) && isset($infos[1])){
                $laFiche['forfait'] = $pdo->getLesFraisForfait($infos[1], $infos[0]);
                $laFiche['hors_forfait'] = $pdo->getLesFraisHorsForfait($infos[1], $infos[0]);
                $laFiche['mois'] = $infos[0];
                $laFiche['visiteur'] = $infos[1];
            }else {
                $laFiche['forfait'] = $laFiche['hors_forfait'] = [];
            }

            if(empty($laFiche['forfait']) && empty($laFiche['hors_forfait'])){
                setFlash("La fiche demandée n'existe pas");
                header('location:index.php?uc=mission2&action=suiviDePaiement');
                die();
            }
        }

        require "vues/v_suiviPaiement.php";
        break;
   
   case 'creationpdf':
        if(isset($_GET['fiche'])){
            $infos = explode('-', $_GET['fiche']);
            if(isset($infos[0]) && isset($infos[1])){
                $laFiche['visiteur'] = $pdo->getVisiteur($infos[1]);
                $laFiche['forfait'] = $pdo->getLesFraisForfait($infos[1], $infos[0]);
                $laFiche['hors_forfait'] = $pdo->getLesFraisHorsForfait($infos[1], $infos[0]);
            }else {
                $laFiche['forfait'] = $laFiche['hors_forfait'] = [];
            }
            if(empty($laFiche['forfait']) && empty($laFiche['hors_forfait'])){
                setFlash("La fiche demandée n'existe pas");
                header('location:index.php?uc=mission2&action=suiviDePaiement');
                die();
            }
        }
        require "vues/creationpdf.php";
        creationpdf($laFiche);
        break;
        
     case 'miseEnPaiement':
        $pdo->miseEnPaiement($_POST['visiteur'], $_POST['mois']);
        setFlash("Mise en paiement effectué");
        header('location:index.php?uc=mission2&action=suiviDePaiement');
        break;
}