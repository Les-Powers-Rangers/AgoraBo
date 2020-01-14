<?php
// src/Controller/PegisController.php
namespace App\Controller;

use PdoJeux;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

require_once 'modele/class.PdoJeux.inc.php';

class PegisController extends AbstractController
{
    /**
     * @Route("/pegis", name="pegis_afficher")
     */
    public function index(SessionInterface $session)
    {
        if ($session->has('idUtilisateur')) {
            $db = PdoJeux::getPdoJeux();
            return $this->afficherPegis($db, -1, -1, 'rien');
        } else {
            return $this->render('connexion.html.twig');
        }
    }

    /**
     * fonction pour afficher la liste des pegis
     * @param $db
     * @param $idPegiModif  positionné si demande de modification
     * @param $idPegiNotif  positionné si mise à jour dans la vue
     * @param $notification  pour notifier la mise à jour dans la vue
     */
    private function afficherPegis(PdoJeux $db, int $idPegiModif, int $idPegiNotif, string $notification)
    {
        $tbMembres = $db->getLesPersonnes_HP();
        $tbPegis = $db->getLesPegis();
        return $this->render('lesPegis.html.twig', array(
            'menuActif' => 'Jeux',
            'tbPegis' => $tbPegis,
            'tbMembres' => $tbMembres,
            'idPegiModif' => $idPegiModif,
            'idPegiNotif' => $idPegiNotif,
            'notification' => $notification
        ));
    }

    /**
     * @Route("/pegis/ajouter", name="pegis_ajouter")
     */
    public function ajouter(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        if (!empty($request->request->get('txtLibPegi'))) {
            $idPegiNotif = $db->ajouterPegi($request->request->get('txtLibPegi'), $request->request->get('lstPersonnes'));
            $notification = 'Ajouté';
        }
        return $this->afficherPegis($db, -1, $idPegiNotif, $notification);
    }

    /**
     * @Route("/pegis/demandermodifier", name="pegis_demandermodifier")
     */
    public function demanderModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        return $this->afficherPegis($db, $request->request->get('txtIdPegi'), -1, 'rien');
    }

    /**
     * @Route("/pegis/validerModifier", name="pegis_validerModifier")
     */
    public function validerModifier(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->modifierPegi($request->request->get('txtIdPegi'), $request->request->get('txtLibPegi'), $request->request->get('lstPersonnes'));
        return $this->afficherPegis($db, -1, $request->request->get('txtIdPegi'), 'Modifié');
    }

    /**
     * @Route("/pegis/supprimer", name="pegis_supprimer")
     */
    public function supprimer(SessionInterface $session, Request $request)
    {
        $db = PdoJeux::getPdoJeux();
        $db->supprimerPegi($request->request->get('txtIdPegi'));
        $this->addFlash(
            'success', 'Le pegi a été supprimé'
        );

        return $this->afficherPegis($db, -1, -1, 'rien');
    }
}
