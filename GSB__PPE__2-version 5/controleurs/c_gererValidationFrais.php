<?php
include("vues/v_sommaire.php");
$action = $_REQUEST['action'];
$idUtilisateur = $_SESSION['idUtilisateur'];
switch($action){
    
    case "demandeValiderFrais": {
        $part = isset($_GET['part']) ? $_GET['part'] : '1';
        $liste_mois = $pdo->getLesMoisNonValides();
        $aValider = [];
        $moisAValider = [];
        foreach($liste_mois as $mois){
            $anneeCourant = substr($mois["mois"], 0, 4);
            $moisCourant = substr($mois["mois"], 4, 2);
            if(!array_key_exists($anneeCourant, $aValider)){
                $aValider[$anneeCourant] = [];
            }
            
            if(!in_array($moisCourant, $aValider[$anneeCourant])){
                $aValider[$anneeCourant][] = $moisCourant;
            }
        }
        if($part === "2"){
            $visiteurs = $pdo->getVisiteursParDate($_GET['lstmois']);
        }
        if(isset($_GET['lstvisiteurs'])){
            $afficherFiche = true;
            $fiche["forfait"] = $pdo->getLesFraisForfait($_GET['lstvisiteurs'], $_GET['lstmois']);
            $fiche["horsForfait"] = $pdo->getLesFraisHorsForfait($_GET['lstvisiteurs'], $_GET['lstmois']);
        }
        include("vues/v_listeMoisComptable.php");
        break;
    }

    case "actualiserFrais": {
        $pdo->majFraisForfait($_POST['idvisiteur'], $_POST['mois'], $_POST['frais']);
        setFlash("Informations actualisées");
        header("location:index.php?uc=gererValidationFrais&action=demandeValiderFrais&part=2&lstmois={$_POST['mois']}&lstvisiteurs={$_POST['idvisiteur']}");
        break;
    }

    case "supprimerFrais": {
        $pdo->majFraisHorsForfait($_POST['idfrais']);
        setFlash("Informations actualisées");
        header("location:index.php?uc=gererValidationFrais&action=demandeValiderFrais&part=2&lstmois={$_POST['lstmois']}&lstvisiteurs={$_POST['lstvisiteurs']}");
        break;
    }

    case "reporterFrais": {
        $mois = $_POST['lstmois'];
        $visiteur = $_POST['lstvisiteurs'];
        $idFrais = $_POST['idfrais'];
        $libelle = $_POST['libelle'];
        $montant = $_POST['montant'];
        $pdo->reporterHorsForfait($idFrais, $visiteur, $mois, $libelle, $montant);
        header("location:index.php?uc=gererValidationFrais&action=demandeValiderFrais&part=2&lstmois=$mois&lstvisiteurs=$visiteur");
        break;
    }

    case "validerFicheFrais": {
        $pdo->validerFicheFrais($_POST['idvisiteur'], $_POST['mois']);
        setFlash("La fiche a bien été validé");
        header('location:index.php?uc=gererValidationFrais&action=demandeValiderFrais');
        break;
    }
    
    case "voirEtatFrais": {
        $leMois = $_POST['lstMois'];
        $lesMois=$pdo->getLesMoisDisponibles($_POST['lstVisiteur']);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_POST['lstVisiteur'],$leMois);
        $lesFraisForfait= $pdo->getLesFraisForfait($_POST['lstVisiteur'],$leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($_POST['lstVisiteur'],$leMois);
        $numAnnee =substr( $leMois,0,4);
        $numMois =substr( $leMois,4,2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif =  $lesInfosFicheFrais['dateModif'];
        $dateModif =  dateAnglaisVersFrancais($dateModif);
        $infosVisiteur = $pdo->getVisiteur($_POST['lstVisiteur']);
        $visiteur = $infosVisiteur["nom"] . " " . $infosVisiteur["prenom"];
        include("vues/v_etatFrais.php");
    }
    
    case "listeVisiteurDate":{
        
        $visiteurs = $pdo->getVisiteurFraisNonValides($_POST['lstmois']);
        
        include "vues/v_listeVisiteurDate.php";
        
        break;
    }
   
   //
   case "remboursement":{
		
		include("vues/v_remboursement.php");
		break;
		
   }
   
}