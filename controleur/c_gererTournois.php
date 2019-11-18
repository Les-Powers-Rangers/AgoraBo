<?php
if (!isset($_POST['cmdAction'])){
    $action = 'afficherTournois';
}
else {
    // par défaut
    $action = $_POST['cmdAction'];
}

$affichage = null;

switch ($action) {
    case 'afficherInfoTournoi':{
        $annee = $_POST["txtAnneeTournoi"];
        $num = $_POST["txtNumTournoi"];
        $objTournoi = $db->getLeTournoi($annee, $num);
        $affichage = "infoTournoi";
        break;
    }
    case 'modifierTournoi':{
        $annee = $_POST["txtAnneeTournoi"];
        $num = $_POST["txtNumTournoi"];
        $objTournoi = $db->getLeTournoi($annee, $num);
        $affichage = "modifierTournoi";
        break;
    }
    case 'creerTournoi':{
        $affichage = "creerTournoi";
        break;
    }
    case 'validerModif':{

        $journees = [];

        $dateTemp = $_POST['dateTournoi0'];
        $heureDebutTemp = $_POST['heureDebutTournoi0'];
        $heureFinTemp = $_POST['heureFinTournoi0'];

        $index = 1;
        while($dateTemp != null) {
            $journee = (object) [
                'date' => $dateTemp,
                'heureDebut' => $heureDebutTemp,
                'heureFin' => $heureFinTemp,
            ];

            array_push($journees, $journee);

            if (array_key_exists('dateTournoi' . $index, $_POST)) {
                $dateTemp = $_POST['dateTournoi' . $index];
                $heureDebutTemp = $_POST['heureDebutTournoi' . $index];
                $heureFinTemp = $_POST['heureFinTournoi' . $index];
            } else {
                $dateTemp = null;
                $heureDebutTemp = null;
                $heureFinTemp = null;
            }

            $index++;
        }

        $objTournoi = (object)[
            'Annee' => $_POST['anneeTournoi'],
            'Numero' => $_POST['numTournoi'],
            'NomTournoi' => $_POST['txtNomTournoi'],
            'Jeu' => $_POST['lstJeux'],
            'Gain' => $_POST['txtGainTournoi'],
            'Format' => $_POST['lstFormat'],
            'Juge' => $_POST['lstJuge'],
            'Animateurs' => $_POST['lstAnimateurs'],
            'Journees' => $journees,
            'Equipements' => $_POST['lstEquipements'],
            'NbParticipants' => $_POST['nbrParticipants'],
        ];
        $db->modifierTournoi($objTournoi);
        break;
    }
    case 'validerCreation':{
        $journees = [];

        $dateTemp = $_POST['dateTournoi0'];
        $heureDebutTemp = $_POST['heureDebutTournoi0'];
        $heureFinTemp = $_POST['heureFinTournoi0'];

        $index = 1;
        while($dateTemp != null) {
            $journee = (object) [
                'date' => $dateTemp,
                'heureDebut' => $heureDebutTemp,
                'heureFin' => $heureFinTemp,
            ];

            array_push($journees, $journee);

            if (array_key_exists('dateTournoi' . $index, $_POST)) {
                $dateTemp = $_POST['dateTournoi' . $index];
                $heureDebutTemp = $_POST['heureDebutTournoi' . $index];
                $heureFinTemp = $_POST['heureFinTournoi' . $index];
            } else {
                $dateTemp = null;
                $heureDebutTemp = null;
                $heureFinTemp = null;
            }

            $index++;
        }

        $objTournoi = (object)[
            'Annee' => $_POST['anneeTournoi'],
            'Numero' => $_POST['numTournoi'],
            'NomTournoi' => $_POST['txtNomTournoi'],
            'Jeu' => $_POST['lstJeux'],
            'Gain' => $_POST['txtGainTournoi'],
            'Format' => $_POST['lstFormat'],
            'Juge' => $_POST['lstJuge'],
            'Animateurs' => $_POST['lstAnimateurs'],
            'Equipements' => $_POST['lstEquipements'],
            'Journees' => $journees,
            'NbParticipants' => $_POST['nbrParticipants'],
        ];
        $db->ajouterTournoi($objTournoi);
        break;
    }
    case 'supprimerTournoi':{
        $annee = $_POST['txtAnneeTournoi'];
        $numTournoi = $_POST['txtNumTournoi'];
        $db->supprimerTournoi($annee, $numTournoi);
        break;
    }
}

$tbEquipements = $db->getLesEquipements();
$tbJeux = $db->getLesJeux_HP();
$tbPersonnes = $db->getLesPersonnes_HP();
$tbFormats = $db->getLesFormats();
$tbTournois = $db->getLesTournois();
require "vue/v_lesTournois.php";
?>
