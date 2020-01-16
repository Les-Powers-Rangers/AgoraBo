<?php
// src/Controller/EquipementsController.php
namespace App\Controller;

use PdoJeux;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

require_once 'modele/class.PdoJeux.inc.php';

class EquipementsController extends AbstractController
{
    /**
     * @Route("/equipements", name="equipements_afficher")
     */
    public function index(SessionInterface $session)
    {
        if ($session->has('idUtilisateur')) {
            $db = PdoJeux::getPdoJeux();
            return $this->afficherEquipements($db, -1, -1, 'rien');
        } else {
            return $this->render('connexion.html.twig');
        }
    }

    /**
     * fonction pour afficher la liste des equipements
     * @param $db
     * @param $idEquipementsModif  positionné si demande de modification
     * @param $idEquipementsNotif  positionné si mise à jour ndans la vue
     * @param $notification  pour notifier la mise à jour dans la vue
     */
    private function afficherEquipements(PdoJeux $db, int $idEquipementsModif, int $idEquipementsNotif, string $notification)
    {
        /** @var PdoJeux $db */
        $tbEquipements = $db->getLesEquipements();
        $tbJeuxVideo = $db->getLesJeuxVideo();
        $tbPlateformes = $db->getLesPlateformes();
        $tbFormats = $db->getLesFormats();
        $tbCategories = $db->getLesCategories();

        return $this->render('lesEquipements.html.twig', array(
            'menuActif' => 'Jeux',
            'tbEquipements' => $tbEquipements,
            'tbJeuxVideo' => $tbJeuxVideo,
            'tbPlateformes' => $tbPlateformes,
            'tbFormats' => $tbFormats,
            'tbCategories' => $tbCategories,
            'idEquipementsModif' => $idEquipementsModif,
            'idEquipementsNotif' => $idEquipementsNotif,
            'notification' => $notification
        ));
    }

    /**
     * @Route("/equipements/ajouter", name="equipements_ajouter")
     */
    public function ajouter(SessionInterface $session, Request $request)
    {
        /** @var PdoJeux $db */
        $db = PdoJeux::getPdoJeux();
        if (!empty($request->request->get('txtNumeroEquipement'))) {
            // Création de l'objet
            $objEquipement = (Object)[
                "NumeroEquipement" => $request->request->get('txtNumeroEquipement'),
                "AnneeEquipement" => $request->request->get('txtAnneeEquipement'),
                "NomEquipement" => $request->request->get('txtNomEquipement'),
                "NbParticipantsEquipement" => $request->request->get('txtNbParticipantsEquipement'),
                "GainEquipement" => 0, //$request->request->get('txtGainEquipement'),
                "Jeu" => $request->request->get('lstJeux'),
                "Format" => $request->request->get('lstFormats'),
                "Categorie" => $request->request->get('lstCategories')
            ];
            $idEquipementsNotif = $db->ajouterEquipement($objEquipement);
            $notification = 'Ajouté';
        }
        return $this->afficherEquipements($db, -1, $idEquipementsNotif, $notification);
    }

    /**
     * @Route("/equipements/demandermodifier", name="equipements_demandermodifier")
     */
    public function demanderModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        return $this->afficherEquipements($db, $request->request->get('txtIdEquipements'), -1, 'rien');
    }

    /**
     * @Route("/equipements/validerModifier", name="equipements_validerModifier")
     */
    public function validerModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->modifierEquipement($request->request->get('txtIdEquipements'), $request->request->get('txtLibEquipements'), $request->request->get('txtNbEquipementsDispo'));
        return $this->afficherEquipements($db, -1, $request->request->get('txtIdEquipements'), 'Modifié');
    }

    /**
     * @Route("/equipements/supprimer", name="equipements_supprimer")
     */
    public function supprimer(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->supprimerEquipement($request->request->get('txtIdEquipements'));
        $this->addFlash(
            'success', 'Le equipements a été supprimé'
        );

        return $this->afficherEquipements($db, -1, -1, 'rien');
    }
}
