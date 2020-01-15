<?php
// src/Controller/TournoisController.php
namespace App\Controller;

use PdoJeux;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

require_once 'modele/class.PdoJeux.inc.php';

class TournoisController extends AbstractController
{
    /**
     * @Route("/tournois", name="tournois_afficher")
     */
    public function index(SessionInterface $session)
    {
        if ($session->has('idUtilisateur')) {
            $db = PdoJeux::getPdoJeux();
            return $this->afficherTournois($db, -1, -1, 'rien');
        } else {
            return $this->render('connexion.html.twig');
        }
    }

    /**
     * fonction pour afficher la liste des tournois
     * @param $db
     * @param $idTournoisModif  positionné si demande de modification
     * @param $idTournoisNotif  positionné si mise à jour ndans la vue
     * @param $notification  pour notifier la mise à jour dans la vue
     */
    private function afficherTournois(PdoJeux $db, int $idTournoisModif, int $idTournoisNotif, string $notification)
    {
        /** @var PdoJeux $db */
        $tbTournois = $db->getLesTournois();
        $tbJeuxVideo = $db->getLesJeuxVideo();
        $tbPlateformes = $db->getLesPlateformes();
        $tbFormats = $db->getLesFormats();
        $tbCategories = $db->getLesCategories();

        return $this->render('lesTournois.html.twig', array(
            'menuActif' => 'Jeux',
            'tbTournois' => $tbTournois,
            'tbJeuxVideo' => $tbJeuxVideo,
            'tbPlateformes' => $tbPlateformes,
            'tbFormats' => $tbFormats,
            'tbCategories' => $tbCategories,
            'idTournoisModif' => $idTournoisModif,
            'idTournoisNotif' => $idTournoisNotif,
            'notification' => $notification
        ));
    }

    /**
     * @Route("/tournois/ajouter", name="tournois_ajouter")
     */
    public function ajouter(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        if (!empty($request->request->get('txtLibTournois'))) {
            $idTournoisNotif = $db->ajouterTournois($request->request->get('txtLibTournois'), $request->request->get('txtNbTournoisDispo'));
            $notification = 'Ajouté';
        }
        return $this->afficherTournois($db, -1, $idTournoisNotif, $notification);
    }

    /**
     * @Route("/tournois/demandermodifier", name="tournois_demandermodifier")
     */
    public function demanderModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        return $this->afficherTournois($db, $request->request->get('txtIdTournois'), -1, 'rien');
    }

    /**
     * @Route("/tournois/validerModifier", name="tournois_validerModifier")
     */
    public function validerModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->modifierTournois($request->request->get('txtIdTournois'), $request->request->get('txtLibTournois'), $request->request->get('txtNbTournoisDispo'));
        return $this->afficherTournois($db, -1, $request->request->get('txtIdTournois'), 'Modifié');
    }

    /**
     * @Route("/tournois/supprimer", name="tournois_supprimer")
     */
    public function supprimer(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->supprimerTournois($request->request->get('txtIdTournois'));
        $this->addFlash(
            'success', 'Le tournois a été supprimé'
        );

        return $this->afficherTournois($db, -1, -1, 'rien');
    }
}
